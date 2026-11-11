<?php

namespace App\Http\Controllers;

use App\Models\LearningContent;
use App\Models\StudySchedule;
use App\Services\AILearningContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LearningContentController extends Controller
{
    /**
     * Hiển thị danh sách nội dung học tập.
     */
    public function index()
    {
        $contents = LearningContent::where('user_id', Auth::id())
            ->orderByRaw("
                CASE status
                    WHEN 'in_progress' THEN 1
                    WHEN 'pending' THEN 2
                    WHEN 'completed' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('created_at')
            ->get();

        return view(
            'learning.index',
            compact('contents')
        );
    }


    /**
     * Hiển thị chi tiết nội dung học tập.
     */
    public function show(LearningContent $learning)
    {
        $this->authorizeOwner($learning);

        $learning->load('exercises');

        return view(
            'learning.show',
            compact('learning')
        );
    }


    /**
     * Hiển thị form thêm nội dung học tập.
     */
    public function create()
    {
        return view('learning.create');
    }


    /**
     * Lưu nội dung học tập mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'subject' => [
                'nullable',
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

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                ]),
            ],
        ], [
            'title.required' =>
                'Vui lòng nhập tên bài học.',

            'title.max' =>
                'Tên bài học không được vượt quá 255 ký tự.',

            'description.max' =>
                'Mô tả không được vượt quá 1000 ký tự.',

            'subject.max' =>
                'Tên môn học không được vượt quá 255 ký tự.',

            'difficulty.required' =>
                'Vui lòng chọn trình độ.',

            'difficulty.in' =>
                'Trình độ không hợp lệ.',

            'status.required' =>
                'Vui lòng chọn trạng thái.',

            'status.in' =>
                'Trạng thái không hợp lệ.',
        ]);

        LearningContent::create([
            'user_id' =>
                Auth::id(),

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'content' =>
                $validated['content'] ?? null,

            'subject' =>
                $validated['subject'] ?? null,

            'difficulty' =>
                $validated['difficulty'],

            'status' =>
                $validated['status'],
        ]);

        return redirect()
            ->route('learning.index')
            ->with(
                'success',
                'Đã thêm nội dung học tập thành công.'
            );
    }


    /**
     * Tạo nội dung học tập bằng AI
     * dựa trên lịch học cá nhân đã được AI đề xuất
     * và người dùng đã chấp nhận.
     */
    public function generateFromSchedule(
        StudySchedule $schedule,
        AILearningContentService $aiLearningContentService
    ) {
        /*
        |--------------------------------------------------------------------------
        | 1. Kiểm tra quyền sở hữu lịch học
        |--------------------------------------------------------------------------
        */

        if (
            (int) $schedule->user_id !==
            (int) Auth::id()
        ) {
            abort(
                403,
                'Bạn không có quyền sử dụng lịch học này.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 2. Chỉ cho phép tạo nội dung từ lịch học AI
        |--------------------------------------------------------------------------
        */

        if ($schedule->source !== 'ai') {
            return redirect()
                ->route('study-schedules.index')
                ->with(
                    'error',
                    'Chỉ có thể tạo nội dung AI từ lịch học được AI đề xuất.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | 3. Kiểm tra trạng thái lịch học
        |--------------------------------------------------------------------------
        |
        | Lịch AI phải đang ở trạng thái planned.
        |
        */

        if ($schedule->status !== 'planned') {
            return redirect()
                ->route('study-schedules.index')
                ->with(
                    'error',
                    'Lịch học này không còn ở trạng thái có thể tạo nội dung AI.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | 4. Kiểm tra nội dung AI đã được tạo trước đó chưa
        |--------------------------------------------------------------------------
        |
        | Hiện tại bảng learning_contents chưa có study_schedule_id.
        | Vì vậy tạm thời dùng user + subject + title để chống tạo trùng.
        |
        */

        $aiTitle =
            'AI - '
            . $schedule->subject
            . ': '
            . $schedule->learning_goal;

        $existingContent = LearningContent::where(
            'user_id',
            Auth::id()
        )
            ->where(
                'subject',
                $schedule->subject
            )
            ->where(
                'title',
                $aiTitle
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | 5. Nếu đã tồn tại thì mở nội dung đó
        |--------------------------------------------------------------------------
        */

        if ($existingContent) {
            return redirect()
                ->route(
                    'learning.show',
                    $existingContent->id
                )
                ->with(
                    'success',
                    'Nội dung AI cho lịch học này đã được tạo trước đó.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | 6. Gọi AI tạo nội dung
        |--------------------------------------------------------------------------
        */

        try {

            $learning =
                $aiLearningContentService->generate(
                    Auth::user(),
                    $schedule
                );

        } catch (\Throwable $e) {

            return redirect()
                ->route('study-schedules.index')
                ->with(
                    'error',
                    'Không thể tạo nội dung học bằng AI: '
                    . $e->getMessage()
                );
        }


        /*
        |--------------------------------------------------------------------------
        | 7. Chuyển đến nội dung vừa được AI tạo
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'learning.show',
                $learning->id
            )
            ->with(
                'success',
                'AI đã tạo nội dung học tập thành công.'
            );
    }


    /**
     * Hiển thị form chỉnh sửa nội dung học tập.
     */
    public function edit(LearningContent $learning)
    {
        $this->authorizeOwner($learning);

        return view(
            'learning.edit',
            compact('learning')
        );
    }


    /**
     * Cập nhật nội dung học tập.
     */
    public function update(
        Request $request,
        LearningContent $learning
    ) {
        $this->authorizeOwner($learning);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'subject' => [
                'nullable',
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

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                ]),
            ],
        ], [
            'title.required' =>
                'Vui lòng nhập tên bài học.',

            'title.max' =>
                'Tên bài học không được vượt quá 255 ký tự.',

            'description.max' =>
                'Mô tả không được vượt quá 1000 ký tự.',

            'subject.max' =>
                'Tên môn học không được vượt quá 255 ký tự.',

            'difficulty.required' =>
                'Vui lòng chọn trình độ.',

            'difficulty.in' =>
                'Trình độ không hợp lệ.',

            'status.required' =>
                'Vui lòng chọn trạng thái.',

            'status.in' =>
                'Trạng thái không hợp lệ.',
        ]);

        $learning->update([
            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'content' =>
                $validated['content'] ?? null,

            'subject' =>
                $validated['subject'] ?? null,

            'difficulty' =>
                $validated['difficulty'],

            'status' =>
                $validated['status'],
        ]);

        return redirect()
            ->route('learning.index')
            ->with(
                'success',
                'Đã cập nhật nội dung học tập thành công.'
            );
    }


    /**
     * Xóa nội dung học tập.
     */
    public function destroy(LearningContent $learning)
    {
        $this->authorizeOwner($learning);

        $learning->delete();

        return redirect()
            ->route('learning.index')
            ->with(
                'success',
                'Đã xóa nội dung học tập thành công.'
            );
    }


    /**
     * Bắt đầu học nội dung.
     *
     * Chỉ cho phép:
     *
     * pending → in_progress
     */
    public function start(LearningContent $learning)
    {
        $this->authorizeOwner($learning);

        /*
        |--------------------------------------------------------------------------
        | Chỉ bắt đầu nếu nội dung đang ở trạng thái pending
        |--------------------------------------------------------------------------
        */

        if ($learning->status === 'pending') {

            $learning->update([
                'status' => 'in_progress',
            ]);
        }

        return redirect()
            ->route(
                'learning.show',
                $learning->id
            )
            ->with(
                'success',
                'Đã bắt đầu học nội dung này.'
            );
    }


    /**
     * Cập nhật nhanh trạng thái học tập.
     */
    public function updateStatus(
        Request $request,
        LearningContent $learning
    ) {
        $this->authorizeOwner($learning);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'in_progress',
                    'completed',
                ]),
            ],
        ]);

        $learning->update([
            'status' =>
                $validated['status'],
        ]);

        return redirect()
            ->route('learning.index')
            ->with(
                'success',
                'Đã cập nhật trạng thái học tập.'
            );
    }


    /**
     * Kiểm tra nội dung có thuộc tài khoản hiện tại không.
     */
    private function authorizeOwner(
        LearningContent $learning
    ) {
        if (
            (int) $learning->user_id !==
            (int) Auth::id()
        ) {
            abort(
                403,
                'Bạn không có quyền truy cập nội dung này.'
            );
        }
    }
}