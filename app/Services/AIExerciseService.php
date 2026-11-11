<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\ExerciseQuestion;
use App\Models\LearningContent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AIExerciseService
{
    protected GroqService $groqService;

    protected PersonalizationService $personalizationService;

    public function __construct(
        GroqService $groqService,
        PersonalizationService $personalizationService
    ) {
        $this->groqService = $groqService;

        $this->personalizationService =
            $personalizationService;
    }

    /**
     * AI tạo một bộ bài tập từ nội dung học tập.
     */
    public function generate(
        User $user,
        LearningContent $learningContent
    ): Exercise {

        /*
         * Kiểm tra quyền sở hữu nội dung.
         */
        if (
            (int) $learningContent->user_id !==
            (int) $user->id
        ) {
            throw new RuntimeException(
                'Bạn không có quyền tạo bài tập từ nội dung học này.'
            );
        }

        /*
         * Lấy dữ liệu cá nhân hóa.
         */
        $personalization =
            $this->personalizationService
                ->getRecommendation($user);

        /*
         * Trình độ người học.
         */
        $learningLevel =
            optional($user->profile)->learning_level
            ?? 'beginner';

        /*
         * Mục tiêu học tập.
         */
        $goals =
            $personalization['goals']
            ?? [];

        $goalText =
            'Chưa có mục tiêu cụ thể.';

        if (!empty($goals)) {

            $goal = $goals[0];

            $goalText =
                ($goal->title ?? 'Mục tiêu học tập')
                . ' - môn '
                . (
                    $goal->subject
                    ?? $learningContent->subject
                    ?? 'Chưa xác định'
                );
        }

        /*
         * Kết quả học tập trước đó.
         */
        $results =
            $personalization['results_by_content']
            ?? [];

        $resultText =
            'Chưa có kết quả bài tập trước đó.';

        if (!empty($results)) {

            $lines = [];

            foreach (
                array_slice($results, 0, 5)
                as $result
            ) {

                $lines[] =
                    '- '
                    . ($result['title'] ?? 'Nội dung')
                    . ': '
                    . ($result['accuracy'] ?? 0)
                    . '% - '
                    . ($result['level'] ?? 'Chưa xác định');
            }

            $resultText =
                implode("\n", $lines);
        }

        /*
        |--------------------------------------------------------------------------
        | SYSTEM PROMPT
        |--------------------------------------------------------------------------
        */

        $systemPrompt = <<<PROMPT
Bạn là AI tạo bài tập của hệ thống AIStudy.

Nhiệm vụ của bạn là tạo một bộ bài tập để kiểm tra
và củng cố kiến thức mà người học vừa học.

NGUYÊN TẮC:

- Chỉ tạo câu hỏi dựa trên nội dung bài học được cung cấp.
- Không hỏi kiến thức nằm ngoài nội dung bài học.
- Câu hỏi phải phù hợp với trình độ người học.
- Câu hỏi phải rõ ràng, không mơ hồ.
- Không tạo câu hỏi trùng nhau.
- Mỗi câu phải có đáp án chính xác.
- Mỗi câu phải có giải thích đáp án.
- Có câu hỏi từ cơ bản đến vận dụng.
- Ưu tiên kiểm tra khả năng hiểu và áp dụng kiến thức.
- Không sử dụng kiến thức không có trong bài học nếu không thể suy ra hợp lý.
- Chỉ trả về JSON hợp lệ.
- Không trả về Markdown.
- Không trả về ```json.
- Không viết lời giải thích bên ngoài JSON.

BỘ BÀI TẬP GỒM ĐÚNG 8 CÂU:

- 3 câu multiple_choice
- 2 câu multiple_select
- 2 câu true_false
- 1 câu essay

CẤU TRÚC JSON:

{
    "title": "Tên bộ bài tập",
    "description": "Mô tả bộ bài tập",
    "questions": [
        {
            "question": "Nội dung câu hỏi",
            "question_type": "multiple_choice",
            "options": {
                "a": "Đáp án A",
                "b": "Đáp án B",
                "c": "Đáp án C",
                "d": "Đáp án D"
            },
            "correct_answer": ["a"],
            "explanation": "Giải thích đáp án",
            "points": 1
        }
    ]
}

QUY TẮC MULTIPLE_CHOICE:

- Có đúng 4 lựa chọn.
- Các key phải là a, b, c, d.
- correct_answer chỉ có đúng 1 phần tử.

QUY TẮC MULTIPLE_SELECT:

- Có đúng 4 lựa chọn.
- Các key phải là a, b, c, d.
- correct_answer phải có ít nhất 2 phần tử.

QUY TẮC TRUE_FALSE:

options phải là:

{
    "true": "Đúng",
    "false": "Sai"
}

correct_answer phải là:

["true"]

hoặc:

["false"]

QUY TẮC ESSAY:

- options phải là null.
- correct_answer phải là [].
- explanation phải mô tả những ý chính mà câu trả lời tốt cần có.

TỔNG CỘNG PHẢI ĐÚNG 8 CÂU.

PROMPT;

        /*
        |--------------------------------------------------------------------------
        | USER PROMPT
        |--------------------------------------------------------------------------
        */

        $userPrompt = <<<PROMPT
Hãy tạo một bộ bài tập cho người học AIStudy.

========================
THÔNG TIN NGƯỜI HỌC
========================

Trình độ:
{$learningLevel}

Mục tiêu:
{$goalText}

========================
NỘI DUNG HỌC TẬP
========================

Tiêu đề:
{$learningContent->title}

Môn học:
{$learningContent->subject}

Mức độ:
{$learningContent->difficulty}

Mô tả:
{$learningContent->description}

Nội dung bài học:

{$learningContent->content}

========================
KẾT QUẢ HỌC TẬP TRƯỚC ĐÓ
========================

{$resultText}

========================
YÊU CẦU
========================

Tạo đúng 8 câu hỏi dựa trên bài học ở trên.

Phân bố chính xác:

3 câu multiple_choice
2 câu multiple_select
2 câu true_false
1 câu essay

Ưu tiên:

- Kiểm tra kiến thức quan trọng.
- Kiểm tra khả năng hiểu.
- Kiểm tra khả năng phân biệt khái niệm.
- Kiểm tra khả năng áp dụng.
- Có câu hỏi vận dụng phù hợp với trình độ.

Không hỏi kiến thức nằm ngoài bài học.

Chỉ trả về JSON hợp lệ.

Không thêm bất kỳ văn bản nào bên ngoài JSON.

PROMPT;

        /*
        |--------------------------------------------------------------------------
        | GỌI GROQ
        |--------------------------------------------------------------------------
        */

        $response =
            $this->groqService->chat([
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ]);

        if (!$response) {
            throw new RuntimeException(
                'Groq không trả về dữ liệu bài tập.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | XỬ LÝ JSON
        |--------------------------------------------------------------------------
        */

        $json =
            trim($response);

        /*
         * Phòng trường hợp AI vẫn trả về Markdown.
         */
        if (
            str_starts_with(
                $json,
                '```'
            )
        ) {

            $json =
                preg_replace(
                    '/^```(?:json)?\s*/i',
                    '',
                    $json
                );

            $json =
                preg_replace(
                    '/\s*```$/',
                    '',
                    $json
                );
        }

        $json =
            trim($json);

        $data =
            json_decode(
                $json,
                true
            );

        /*
         * Kiểm tra JSON.
         */
        if (
            !is_array($data)
            || empty($data['questions'])
            || !is_array(
                $data['questions']
            )
        ) {
            throw new RuntimeException(
                'AI trả về dữ liệu bài tập không đúng định dạng JSON.'
            );
        }

        /*
         * Phải đúng 8 câu.
         */
        if (
            count($data['questions']) !== 8
        ) {
            throw new RuntimeException(
                'AI không tạo đủ 8 câu hỏi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA CẤU TRÚC CÂU HỎI
        |--------------------------------------------------------------------------
        */

        $typeCount = [
            'multiple_choice' => 0,
            'multiple_select' => 0,
            'true_false' => 0,
            'essay' => 0,
        ];

        foreach (
            $data['questions']
            as $index => $question
        ) {

            $questionType =
                $question['question_type']
                ?? null;

            if (
                !array_key_exists(
                    $questionType,
                    $typeCount
                )
            ) {
                throw new RuntimeException(
                    'Câu hỏi số '
                    . ($index + 1)
                    . ' có loại câu hỏi không hợp lệ.'
                );
            }

            $typeCount[$questionType]++;

            if (
                empty(
                    $question['question']
                )
            ) {
                throw new RuntimeException(
                    'Câu hỏi số '
                    . ($index + 1)
                    . ' không có nội dung.'
                );
            }

            /*
             * Kiểm tra lựa chọn.
             */
            if (
                in_array(
                    $questionType,
                    [
                        'multiple_choice',
                        'multiple_select',
                        'true_false',
                    ],
                    true
                )
            ) {

                if (
                    !isset(
                        $question['options']
                    )
                    || !is_array(
                        $question['options']
                    )
                ) {
                    throw new RuntimeException(
                        'Câu hỏi số '
                        . ($index + 1)
                        . ' không có đáp án lựa chọn hợp lệ.'
                    );
                }
            }

            /*
             * Multiple choice phải có 1 đáp án.
             */
            if (
                $questionType ===
                'multiple_choice'
            ) {

                if (
                    count(
                        $question['correct_answer']
                        ?? []
                    ) !== 1
                ) {
                    throw new RuntimeException(
                        'Câu multiple choice số '
                        . ($index + 1)
                        . ' phải có đúng 1 đáp án.'
                    );
                }
            }

            /*
             * Multiple select phải có ít nhất 2 đáp án.
             */
            if (
                $questionType ===
                'multiple_select'
            ) {

                if (
                    count(
                        $question['correct_answer']
                        ?? []
                    ) < 2
                ) {
                    throw new RuntimeException(
                        'Câu multiple select số '
                        . ($index + 1)
                        . ' phải có ít nhất 2 đáp án đúng.'
                    );
                }
            }

            /*
             * Essay không có options.
             */
            if (
                $questionType ===
                'essay'
            ) {

                $data['questions'][$index][
                    'options'
                ] = null;

                $data['questions'][$index][
                    'correct_answer'
                ] = [];
            }
        }

        /*
         * Kiểm tra đúng phân bố 8 câu.
         */
        if (
            $typeCount['multiple_choice'] !== 3
            || $typeCount['multiple_select'] !== 2
            || $typeCount['true_false'] !== 2
            || $typeCount['essay'] !== 1
        ) {
            throw new RuntimeException(
                'AI trả về không đúng cơ cấu 3 multiple choice, 2 multiple select, 2 true/false và 1 essay.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LƯU DATABASE
        |--------------------------------------------------------------------------
        */

        return DB::transaction(
            function () use (
                $user,
                $learningContent,
                $data,
                $learningLevel
            ) {

                $questions =
                    $data['questions'];

                /*
                 * Câu đầu tiên được dùng để
                 * tương thích với cấu trúc Exercise cũ.
                 */
                $firstQuestion =
                    $questions[0];

                $legacyOptions =
                    $this->getLegacyOptions(
                        $firstQuestion
                    );

                $legacyCorrectOption =
                    $this->getLegacyCorrectOption(
                        $firstQuestion
                    );

                /*
                 * Nếu câu đầu tiên không phải
                 * multiple choice thì dùng giá trị
                 * tương thích để đáp ứng NOT NULL.
                 */
                if (
                    !$legacyCorrectOption
                ) {

                    $legacyCorrectOption =
                        'a';
                }

                /*
                 * Các cột option cũ không được NULL.
                 */
                $legacyOptions['a'] =
                    $legacyOptions['a']
                    ?? '';

                $legacyOptions['b'] =
                    $legacyOptions['b']
                    ?? '';

                $legacyOptions['c'] =
                    $legacyOptions['c']
                    ?? '';

                $legacyOptions['d'] =
                    $legacyOptions['d']
                    ?? '';

                /*
                 * Tạo Exercise.
                 */
                $exercise =
                    Exercise::create([
                        'user_id' =>
                            $user->id,

                        'learning_content_id' =>
                            $learningContent->id,

                        'title' =>
                            $data['title']
                            ?? (
                                'Bài tập AI - '
                                . $learningContent->title
                            ),

                        /*
                         * Cột cũ bắt buộc NOT NULL.
                         */
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
                            $legacyCorrectOption,

                        'explanation' =>
                            $firstQuestion[
                                'explanation'
                            ]
                            ?? null,

                        'difficulty' =>
                            in_array(
                                $learningLevel,
                                [
                                    'beginner',
                                    'intermediate',
                                    'advanced',
                                ],
                                true
                            )
                            ? $learningLevel
                            : 'beginner',

                        'status' =>
                            'active',
                    ]);

                /*
                 * Tạo 8 ExerciseQuestion.
                 */
                foreach (
                    $questions
                    as $index => $question
                ) {

                    $questionType =
                        $question[
                            'question_type'
                        ];

                    $options =
                        $question['options']
                        ?? null;

                    $correctAnswer =
                        $question[
                            'correct_answer'
                        ]
                        ?? [];

                    /*
                     * Essay không có options
                     * và không có đáp án tự động.
                     */
                    if (
                        $questionType ===
                        'essay'
                    ) {

                        $options = null;

                        $correctAnswer = [];
                    }

                    ExerciseQuestion::create([
                        'exercise_id' =>
                            $exercise->id,

                        'question' =>
                            $question[
                                'question'
                            ],

                        'question_type' =>
                            $questionType,

                        'options' =>
                            $options,

                        'correct_answer' =>
                            $correctAnswer,

                        'explanation' =>
                            $question[
                                'explanation'
                            ]
                            ?? null,

                        'points' =>
                            $question['points']
                            ?? 1,

                        'sort_order' =>
                            $index + 1,
                    ]);
                }

                return $exercise;
            }
        );
    }

    /**
     * Chuyển options AI sang cấu trúc cũ.
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

    /**
     * Lấy đáp án tương thích với cấu trúc cũ.
     */
    private function getLegacyCorrectOption(
        array $question
    ): ?string {

        if (
            ($question['question_type'] ?? null)
            !== 'multiple_choice'
        ) {
            return null;
        }

        $correct =
            $question['correct_answer']
            ?? [];

        return $correct[0]
            ?? null;
    }
}