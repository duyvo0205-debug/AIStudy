<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseAttempt;
use App\Models\ExerciseQuestion;
use App\Models\LearningContent;
use App\Services\AIExerciseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    /**
     * Danh sách bộ bài tập.
     */
    public function index()
    {
        $exercises = Exercise::with([
            'learningContent',
            'questions',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('exercises.index', compact('exercises'));
    }

    /**
     * Form tạo bộ bài tập.
     */
    public function create()
    {
        $contents = LearningContent::where(
            'user_id',
            Auth::id()
        )
            ->orderBy('title')
            ->get();

        return view(
            'exercises.create',
            compact('contents')
        );
    }

    /**
     * Lưu bộ bài tập và nhiều câu hỏi.
     */
    public function store(Request $request)
    {
        $validated = $this->validateExercise($request);

        $this->authorizeLearningContent(
            $validated['learning_content_id'] ?? null
        );

        $exercise = DB::transaction(function () use ($validated) {

            $firstQuestion = $validated['questions'][0];

            /*
             * Giữ dữ liệu ở các cột cũ để tương thích
             * với cấu trúc Exercise hiện tại.
             */
            $legacyOptions =
                $this->getLegacyOptions($firstQuestion);

            $exercise = Exercise::create([
                'user_id' => Auth::id(),

                'learning_content_id' =>
                    $validated['learning_content_id'] ?? null,

                'title' =>
                    $validated['title'],

                'question' =>
                    $firstQuestion['question'],

                'option_a' =>
                    $legacyOptions['a'],

                'option_b' =>
                    $legacyOptions['b'],

                'option_c' =>
                    $legacyOptions['c'],

                'option_d' =>
                    $legacyOptions['d'],

                'correct_option' =>
                    $this->getLegacyCorrectOption(
                        $firstQuestion
                    ),

                'explanation' =>
                    $firstQuestion['explanation'] ?? null,

                'difficulty' =>
                    $validated['difficulty'],

                'status' =>
                    'active',
            ]);

            /*
             * Lưu toàn bộ câu hỏi mới.
             */
            foreach (
                $validated['questions']
                as $index => $questionData
            ) {

                ExerciseQuestion::create([
                    'exercise_id' =>
                        $exercise->id,

                    'question' =>
                        $questionData['question'],

                    'question_type' =>
                        $questionData['question_type'],

                    'options' =>
                        $questionData['options'] ?? null,

                    'correct_answer' =>
                        $questionData['correct_answer'] ?? null,

                    'explanation' =>
                        $questionData['explanation'] ?? null,

                    'points' =>
                        $questionData['points'],

                    'sort_order' =>
                        $index + 1,
                ]);
            }

            return $exercise;
        });

        return redirect()
            ->route('exercises.index')
            ->with(
                'success',
                'Đã tạo bộ bài tập thành công.'
            );
    }

    /**
     * Hiển thị bộ bài tập để làm.
     */
    public function show(Exercise $exercise)
    {
        $this->authorizeOwner($exercise);

        $exercise->load([
            'learningContent',
            'questions',
        ]);

        return view(
            'exercises.show',
            compact('exercise')
        );
    }

    /**
     * Nộp toàn bộ bộ bài tập.
     */
    public function submit(
        Request $request,
        Exercise $exercise
    ) {
        $this->authorizeOwner($exercise);

        $exercise->load('questions');

        if ($exercise->questions->isEmpty()) {
            return redirect()
                ->route(
                    'exercises.show',
                    $exercise
                )
                ->with(
                    'error',
                    'Bài tập chưa có câu hỏi.'
                );
        }

        /*
         * Tạo validation tương ứng với từng loại câu hỏi.
         */
        $rules = [];

        foreach ($exercise->questions as $question) {

            $field =
                'answers.' . $question->id;

            switch ($question->question_type) {

                case 'multiple_choice':
                case 'true_false':

                    $rules[$field] = [
                        'required',
                    ];

                    break;

                case 'multiple_select':

                    $rules[$field] = [
                        'required',
                        'array',
                        'min:1',
                    ];

                    break;

                case 'essay':

                    $rules[$field] = [
                        'required',
                        'string',
                    ];

                    break;
            }
        }

        $validated = $request->validate(
            $rules,
            [
                'required' =>
                    'Vui lòng trả lời câu hỏi.',

                'array' =>
                    'Dữ liệu đáp án không hợp lệ.',

                'answers.*.required' =>
                    'Vui lòng trả lời câu hỏi.',
            ]
        );

        $attempts = [];

        DB::transaction(function () use (
            $exercise,
            $validated,
            &$attempts
        ) {

            foreach ($exercise->questions as $question) {

                $answer =
                    $validated['answers'][$question->id]
                    ?? null;

                /*
                 * Câu tự luận:
                 * hiện tại chưa tự động chấm.
                 */
                if (
                    $question->question_type === 'essay'
                ) {

                    $isCorrect = null;

                    $score = 0;

                } else {

                    $isCorrect =
                        $this->checkAnswer(
                            $question,
                            $answer
                        );

                    $score =
                        $isCorrect
                            ? (float) $question->points
                            : 0;
                }

                /*
                 * Multiple select được lưu dưới dạng JSON.
                 * Các loại còn lại lưu trực tiếp.
                 */
                $selectedOption =
                    is_array($answer)
                        ? json_encode(
                            $answer,
                            JSON_UNESCAPED_UNICODE
                        )
                        : $answer;

                $attempt =
                    ExerciseAttempt::create([
                        'user_id' =>
                            Auth::id(),

                        'exercise_id' =>
                            $exercise->id,

                        'exercise_question_id' =>
                            $question->id,

                        'selected_option' =>
                            $selectedOption,

                        'is_correct' =>
                            $isCorrect,

                        'score' =>
                            $score,
                    ]);

                $attempts[] = $attempt;
            }
        });

        /*
         * Cập nhật trạng thái bài học.
         */
        $this->updateLearningContentStatus(
            $exercise
        );

        return view(
            'exercises.result',
            [
                'exercise' =>
                    $exercise,

                'attempts' =>
                    collect($attempts),
            ]
        );
    }

    /**
     * Form chỉnh sửa bộ bài tập.
     */
    public function edit(Exercise $exercise)
    {
        $this->authorizeOwner($exercise);

        $contents =
            LearningContent::where(
                'user_id',
                Auth::id()
            )
                ->orderBy('title')
                ->get();

        $exercise->load([
            'questions',
        ]);

        return view(
            'exercises.edit',
            [
                'exercise' =>
                    $exercise,

                'contents' =>
                    $contents,
            ]
        );
    }

    /**
     * Cập nhật bộ bài tập và các câu hỏi.
     */
    public function update(
        Request $request,
        Exercise $exercise
    ) {
        $this->authorizeOwner($exercise);

        $validated =
            $this->validateExercise($request);

        $this->authorizeLearningContent(
            $validated['learning_content_id'] ?? null
        );

        DB::transaction(function () use (
            $validated,
            $exercise
        ) {

            $firstQuestion =
                $validated['questions'][0];

            $legacyOptions =
                $this->getLegacyOptions(
                    $firstQuestion
                );

            /*
             * Cập nhật dữ liệu cũ.
             */
            $exercise->update([
                'learning_content_id' =>
                    $validated['learning_content_id']
                    ?? null,

                'title' =>
                    $validated['title'],

                'question' =>
                    $firstQuestion['question'],

                'option_a' =>
                    $legacyOptions['a'],

                'option_b' =>
                    $legacyOptions['b'],

                'option_c' =>
                    $legacyOptions['c'],

                'option_d' =>
                    $legacyOptions['d'],

                'correct_option' =>
                    $this->getLegacyCorrectOption(
                        $firstQuestion
                    ),

                'explanation' =>
                    $firstQuestion['explanation']
                    ?? null,

                'difficulty' =>
                    $validated['difficulty'],
            ]);

            /*
             * Xóa câu hỏi cũ.
             *
             * Attempt cũ vẫn được giữ.
             */
            $exercise->questions()->delete();

            /*
             * Tạo lại danh sách câu hỏi.
             */
            foreach (
                $validated['questions']
                as $index => $questionData
            ) {

                ExerciseQuestion::create([
                    'exercise_id' =>
                        $exercise->id,

                    'question' =>
                        $questionData['question'],

                    'question_type' =>
                        $questionData['question_type'],

                    'options' =>
                        $questionData['options']
                        ?? null,

                    'correct_answer' =>
                        $questionData['correct_answer']
                        ?? null,

                    'explanation' =>
                        $questionData['explanation']
                        ?? null,

                    'points' =>
                        $questionData['points'],

                    'sort_order' =>
                        $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('exercises.index')
            ->with(
                'success',
                'Đã cập nhật bộ bài tập.'
            );
    }

    /**
     * Xóa bộ bài tập.
     */
    public function destroy(
        Exercise $exercise
    ) {
        $this->authorizeOwner($exercise);

        $exercise->delete();

        return redirect()
            ->route('exercises.index')
            ->with(
                'success',
                'Đã xóa bộ bài tập.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AI TẠO BÀI TẬP TỪ NỘI DUNG HỌC
    |--------------------------------------------------------------------------
    */

    public function generateFromLearning(
        LearningContent $learning,
        AIExerciseService $aiExerciseService
    ) {
        /*
         * Kiểm tra quyền sở hữu bài học.
         */
        if (
            (int) $learning->user_id !==
            (int) Auth::id()
        ) {
            abort(
                403,
                'Bạn không có quyền tạo bài tập từ nội dung này.'
            );
        }

        /*
         * Phải có nội dung lý thuyết.
         */
        if (
            empty($learning->content)
        ) {
            return redirect()
                ->route(
                    'learning.show',
                    $learning->id
                )
                ->with(
                    'error',
                    'Nội dung học tập chưa có phần lý thuyết để AI tạo bài tập.'
                );
        }

        /*
         * Không tạo trùng bài tập AI.
         */
        $existingExercise =
            Exercise::where(
                'user_id',
                Auth::id()
            )
                ->where(
                    'learning_content_id',
                    $learning->id
                )
                ->where(
                    'title',
                    'like',
                    'Bài tập AI%'
                )
                ->latest()
                ->first();

        if ($existingExercise) {
            return redirect()
                ->route(
                    'exercises.show',
                    $existingExercise->id
                )
                ->with(
                    'success',
                    'Bài tập AI cho nội dung này đã được tạo trước đó.'
                );
        }

        /*
         * Gọi AI.
         */
        try {

            $exercise =
                $aiExerciseService->generate(
                    Auth::user(),
                    $learning
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route(
                    'learning.show',
                    $learning->id
                )
                ->with(
                    'error',
                    'Không thể tạo bài tập bằng AI: '
                    . $e->getMessage()
                );
        }

        /*
         * Chuyển người dùng sang trang làm bài.
         */
        return redirect()
            ->route(
                'exercises.show',
                $exercise->id
            )
            ->with(
                'success',
                'AI đã tạo bài tập dựa trên nội dung bạn vừa học.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validateExercise(
        Request $request
    ): array {

        return $request->validate(
            [
                'learning_content_id' => [
                    'nullable',
                    'exists:learning_contents,id',
                ],

                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'difficulty' => [
                    'required',
                    Rule::in([
                        'beginner',
                        'intermediate',
                        'advanced',
                    ]),
                ],

                'questions' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'questions.*.question' => [
                    'required',
                    'string',
                ],

                'questions.*.question_type' => [
                    'required',
                    Rule::in([
                        'multiple_choice',
                        'multiple_select',
                        'true_false',
                        'essay',
                    ]),
                ],

                'questions.*.options' => [
                    'nullable',
                    'array',
                ],

                'questions.*.correct_answer' => [
                    'nullable',
                    'array',
                ],

                'questions.*.explanation' => [
                    'nullable',
                    'string',
                ],

                'questions.*.points' => [
                    'required',
                    'numeric',
                    'min:0.1',
                    'max:100',
                ],
            ],
            [
                'title.required' =>
                    'Vui lòng nhập tên bài tập.',

                'questions.required' =>
                    'Vui lòng tạo ít nhất một câu hỏi.',

                'questions.min' =>
                    'Bài tập phải có ít nhất một câu hỏi.',

                'questions.*.question.required' =>
                    'Vui lòng nhập nội dung câu hỏi.',

                'questions.*.question_type.required' =>
                    'Vui lòng chọn loại câu hỏi.',

                'questions.*.points.required' =>
                    'Vui lòng nhập điểm cho câu hỏi.',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA ĐÁP ÁN
    |--------------------------------------------------------------------------
    */

    private function checkAnswer(
        ExerciseQuestion $question,
        mixed $answer
    ): bool {

        $correct =
            $question->correct_answer
            ?? [];

        /*
         * Multiple select.
         */
        if (
            $question->question_type ===
            'multiple_select'
        ) {

            $given =
                is_array($answer)
                    ? $answer
                    : [$answer];

            $given =
                array_map(
                    'strval',
                    $given
                );

            $correct =
                is_array($correct)
                    ? $correct
                    : [$correct];

            $correct =
                array_map(
                    'strval',
                    $correct
                );

            sort($given);
            sort($correct);

            return $given === $correct;
        }

        /*
         * True / False.
         */
        if (
            $question->question_type ===
            'true_false'
        ) {

            $given =
                strtolower(
                    trim(
                        (string) $answer
                    )
                );

            $expected =
                strtolower(
                    trim(
                        (string) (
                            $correct[0]
                            ?? ''
                        )
                    )
                );

            return $given === $expected;
        }

        /*
         * Multiple choice.
         */
        return
            (string) $answer ===
            (string) (
                $correct[0]
                ?? ''
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CẬP NHẬT TRẠNG THÁI NỘI DUNG HỌC
    |--------------------------------------------------------------------------
    */

    private function updateLearningContentStatus(
        Exercise $exercise
    ): void {

        /*
         * Nếu bài tập không liên kết với
         * nội dung học thì không làm gì.
         */
        if (!$exercise->learningContent) {
            return;
        }

        $learningContent =
            $exercise->learningContent;

        /*
         * Lấy toàn bộ attempt của người dùng
         * thuộc các bài tập của nội dung này.
         */
        $attempts =
            ExerciseAttempt::where(
                'user_id',
                Auth::id()
            )
                ->whereHas(
                    'exercise',
                    function ($query) use (
                        $learningContent
                    ) {

                        $query->where(
                            'learning_content_id',
                            $learningContent->id
                        );
                    }
                )
                ->get();

        /*
         * Chỉ tính câu đã được chấm.
         *
         * Essay chưa được AI chấm sẽ có
         * is_correct = null.
         */
        $gradedAttempts =
            $attempts->filter(
                fn ($attempt) =>
                    $attempt->is_correct !== null
            );

        if (
            $gradedAttempts->isEmpty()
        ) {
            return;
        }

        $totalAttempts =
            $gradedAttempts->count();

        $correctAttempts =
            $gradedAttempts
                ->where(
                    'is_correct',
                    true
                )
                ->count();

        $accuracy =
            $totalAttempts > 0
                ? (
                    $correctAttempts /
                    $totalAttempts
                ) * 100
                : 0;

        /*
         * >= 80%:
         * hoàn thành nội dung.
         */
        if ($accuracy >= 80) {

            $learningContent->update([
                'status' =>
                    'completed',
            ]);

        } else {

            $learningContent->update([
                'status' =>
                    'in_progress',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DỮ LIỆU TƯƠNG THÍCH CẤU TRÚC CŨ
    |--------------------------------------------------------------------------
    */

    private function getLegacyOptions(
        array $question
    ): array {

        $options =
            $question['options']
            ?? [];

        return [
            'a' =>
                $options['a']
                ?? null,

            'b' =>
                $options['b']
                ?? null,

            'c' =>
                $options['c']
                ?? null,

            'd' =>
                $options['d']
                ?? null,
        ];
    }

    private function getLegacyCorrectOption(
        array $question
    ): ?string {

        /*
         * Chỉ multiple choice mới có
         * correct_option cũ.
         */
        if (
            $question['question_type'] !==
            'multiple_choice'
        ) {
            return null;
        }

        $correct =
            $question['correct_answer']
            ?? [];

        return $correct[0]
            ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA QUYỀN LEARNING CONTENT
    |--------------------------------------------------------------------------
    */

    private function authorizeLearningContent(
        ?int $learningContentId
    ): void {

        if (!$learningContentId) {
            return;
        }

        $belongsToUser =
            LearningContent::where(
                'id',
                $learningContentId
            )
                ->where(
                    'user_id',
                    Auth::id()
                )
                ->exists();

        if (!$belongsToUser) {

            abort(
                403,
                'Bạn không có quyền sử dụng bài học này.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA QUYỀN SỞ HỮU EXERCISE
    |--------------------------------------------------------------------------
    */

    private function authorizeOwner(
        Exercise $exercise
    ): void {

        if (
            (int) $exercise->user_id !==
            (int) Auth::id()
        ) {

            abort(
                403,
                'Bạn không có quyền truy cập bài tập này.'
            );
        }
    }
}