<?php

namespace App\Http\Controllers;

use App\Models\AIStudyRecommendation;
use App\Models\StudySchedule;
use App\Services\AIStudyRecommendationService;
use App\Services\PersonalizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    protected PersonalizationService $personalizationService;

    protected AIStudyRecommendationService $recommendationService;

    public function __construct(
        PersonalizationService $personalizationService,
        AIStudyRecommendationService $recommendationService
    ) {
        $this->personalizationService = $personalizationService;
        $this->recommendationService = $recommendationService;
    }

    /**
     * Trang đề xuất của AI
     */
    public function index()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | 1. AI tự động kiểm tra thời khóa biểu
        |--------------------------------------------------------------------------
        */

        $this->recommendationService->generateForUser($user);

        /*
        |--------------------------------------------------------------------------
        | 2. Lấy các đề xuất lịch học từ AI
        |--------------------------------------------------------------------------
        */

        $aiRecommendations = AIStudyRecommendation::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('schoolSchedule')
            ->orderBy('recommended_date')
            ->orderBy('recommended_start_time')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3. Lấy dữ liệu cá nhân hóa người học
        |--------------------------------------------------------------------------
        */

        $data = $this->personalizationService
            ->getRecommendation($user);

        /*
        |--------------------------------------------------------------------------
        | 4. Trả dữ liệu sang Blade
        |--------------------------------------------------------------------------
        */

        return view(
            'recommendations.index',
            [
                // Đề xuất lịch học từ AI
                'aiRecommendations' => $aiRecommendations,

                // Dữ liệu cá nhân hóa
                'data' => $data,

                // Nội dung được ưu tiên
                'recommendation' =>
                    $data['recommendation'] ?? null,

                // Danh sách nội dung đề xuất
                'recommendations' =>
                    $data['recommendations'] ?? [],

                // Điều chỉnh học tập
                'adjustment' =>
                    $data['adjustment'] ?? null,

                // Thống kê lộ trình
                'learning_path_summary' =>
                    $data['learning_path_summary'] ?? null,

                // Nội dung tiếp theo
                'next_content' =>
                    $data['next_content'] ?? null,
            ]
        );
    }

    /**
     * Chấp nhận đề xuất lịch học
     */
    public function accept(
        AIStudyRecommendation $recommendation
    ): RedirectResponse {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra quyền sở hữu
        |--------------------------------------------------------------------------
        */

        if (
            (int) $recommendation->user_id !==
            (int) $user->id
        ) {
            abort(
                403,
                'Bạn không có quyền xử lý đề xuất này.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Chỉ xử lý đề xuất đang pending
        |--------------------------------------------------------------------------
        */

        if ($recommendation->status !== 'pending') {
            return redirect()
                ->route('recommendations.index')
                ->with(
                    'error',
                    'Đề xuất này không còn ở trạng thái chờ xử lý.'
                );
        }

        DB::transaction(function () use (
            $recommendation,
            $user
        ) {

            /*
            |--------------------------------------------------------------------------
            | Kiểm tra lịch học đã tồn tại chưa
            |--------------------------------------------------------------------------
            */

            $existingSchedule = StudySchedule::query()
                ->where('user_id', $user->id)
                ->where(
                    'study_date',
                    $recommendation->recommended_date
                )
                ->where(
                    'start_time',
                    $recommendation->recommended_start_time
                )
                ->where(
                    'end_time',
                    $recommendation->recommended_end_time
                )
                ->where(
                    'subject',
                    $recommendation->subject
                )
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Nếu chưa có thì thêm vào lịch học cá nhân
            |--------------------------------------------------------------------------
            */

            if (!$existingSchedule) {

                StudySchedule::create([
                    'user_id' =>
                        $user->id,

                    'title' =>
                        $recommendation->title,

                    'subject' =>
                        $recommendation->subject,

                    'learning_goal' =>
                        $recommendation->learning_goal,

                    'study_content' =>
                        $recommendation->description,

                    'description' =>
                        $recommendation->reason,

                    'study_date' =>
                        $recommendation->recommended_date,

                    'start_time' =>
                        $recommendation->recommended_start_time,

                    'end_time' =>
                        $recommendation->recommended_end_time,

                    'duration_minutes' =>
                        $recommendation->duration_minutes,

                    'priority' =>
                        'medium',

                    'status' =>
                        'planned',

                    'source' =>
                        'ai',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Chuyển đề xuất thành accepted
            |--------------------------------------------------------------------------
            */

            $recommendation->update([
                'status' => 'accepted',
            ]);
        });

        return redirect()
            ->route('recommendations.index')
            ->with(
                'success',
                'Đã chấp nhận đề xuất và thêm vào lịch học cá nhân.'
            );
    }

    /**
     * Từ chối đề xuất
     */
    public function reject(
        AIStudyRecommendation $recommendation
    ): RedirectResponse {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Kiểm tra quyền sở hữu
        |--------------------------------------------------------------------------
        */

        if (
            (int) $recommendation->user_id !==
            (int) $user->id
        ) {
            abort(
                403,
                'Bạn không có quyền xử lý đề xuất này.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Chỉ xử lý pending
        |--------------------------------------------------------------------------
        */

        if ($recommendation->status !== 'pending') {
            return redirect()
                ->route('recommendations.index')
                ->with(
                    'error',
                    'Đề xuất này không còn ở trạng thái chờ xử lý.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Từ chối
        |--------------------------------------------------------------------------
        */

        $recommendation->update([
            'status' => 'rejected',
        ]);

        return redirect()
            ->route('recommendations.index')
            ->with(
                'success',
                'Đã từ chối đề xuất học tập.'
            );
    }
}