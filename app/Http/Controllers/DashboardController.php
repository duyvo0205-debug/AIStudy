<?php

namespace App\Http\Controllers;

use App\Models\AIStudyRecommendation;
use App\Models\ExerciseAttempt;
use App\Models\LearningContent;
use App\Models\LearningGoal;
use App\Models\SchoolSchedule;
use App\Models\StudySchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $today = Carbon::today();
        $sevenDaysAgo = $today->copy()->subDays(6);

        /*
        |--------------------------------------------------------------------------
        | 1. THỐNG KÊ NỘI DUNG HỌC TẬP
        |--------------------------------------------------------------------------
        */

        $totalContents = LearningContent::where('user_id', $userId)
            ->count();

        $completedContents = LearningContent::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $contentCompletion = $totalContents > 0
            ? round(($completedContents / $totalContents) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 2. THỐNG KÊ LỊCH HỌC
        |--------------------------------------------------------------------------
        */

        $totalSchedules = StudySchedule::where('user_id', $userId)
            ->count();

        $completedSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $plannedSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'planned')
            ->count();

        $totalStudyMinutes = StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('duration_minutes');

        $totalStudyHours = intdiv((int) $totalStudyMinutes, 60);
        $remainingMinutes = (int) $totalStudyMinutes % 60;

        /*
        |--------------------------------------------------------------------------
        | 3. THỐNG KÊ BÀI TẬP
        |--------------------------------------------------------------------------
        */

        $totalAttempts = ExerciseAttempt::where('user_id', $userId)
            ->count();

        $correctAttempts = ExerciseAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->count();

        $accuracy = $totalAttempts > 0
            ? round(($correctAttempts / $totalAttempts) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | 4. CHUỖI NGÀY HỌC LIÊN TIẾP
        |--------------------------------------------------------------------------
        */

        $activityDates = collect();

        ExerciseAttempt::where('user_id', $userId)
            ->whereDate('created_at', '<=', $today)
            ->pluck('created_at')
            ->each(function ($date) use ($activityDates) {
                $activityDates->push(
                    Carbon::parse($date)->toDateString()
                );
            });

        StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('study_date', '<=', $today)
            ->pluck('study_date')
            ->each(function ($date) use ($activityDates) {
                $activityDates->push(
                    Carbon::parse($date)->toDateString()
                );
            });

        LearningContent::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereDate('updated_at', '<=', now())
            ->pluck('updated_at')
            ->each(function ($date) use ($activityDates) {
                $activityDates->push(
                    Carbon::parse($date)->toDateString()
                );
            });

        $activityDates = $activityDates
            ->unique()
            ->sort()
            ->values();

        $studyStreak = 0;
        $checkDate = $today->copy();

        while ($activityDates->contains($checkDate->toDateString())) {
            $studyStreak++;
            $checkDate->subDay();
        }

        // Nếu hôm nay chưa có hoạt động, kiểm tra từ hôm qua.
        if (
            $studyStreak === 0 &&
            $activityDates->contains($today->copy()->subDay()->toDateString())
        ) {
            $checkDate = $today->copy()->subDay();

            while ($activityDates->contains($checkDate->toDateString())) {
                $studyStreak++;
                $checkDate->subDay();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. BIỂU ĐỒ HOẠT ĐỘNG 7 NGÀY
        |--------------------------------------------------------------------------
        */

        $attemptsByDate = ExerciseAttempt::where('user_id', $userId)
            ->whereBetween('created_at', [
                $sevenDaysAgo->copy()->startOfDay(),
                now(),
            ])
            ->get()
            ->groupBy(function ($attempt) {
                return Carbon::parse($attempt->created_at)->toDateString();
            });

        $weeklyProgress = collect();

        for ($i = 0; $i < 7; $i++) {
            $date = $sevenDaysAgo->copy()->addDays($i);

            $weeklyProgress->push([
                'label' => $date->format('d/m'),
                'day' => $date->format('D'),
                'attempts' => $attemptsByDate
                    ->get($date->toDateString(), collect())
                    ->count(),
            ]);
        }

        $maxWeeklyAttempts = max(
            1,
            (int) $weeklyProgress->max('attempts')
        );

        /*
        |--------------------------------------------------------------------------
        | 6. LỊCH HỌC SẮP TỚI
        |--------------------------------------------------------------------------
        */

        $upcomingSchedules = collect();

        $endUpcomingDate = $today->copy()->addDays(2);

$personalSchedules = StudySchedule::where('user_id', $userId)
    ->where('status', 'planned')
    ->whereBetween('study_date', [
        $today->toDateString(),
        $endUpcomingDate->toDateString(),
    ])
    ->get();

        foreach ($personalSchedules as $schedule) {
            $upcomingSchedules->push((object) [
                'id' => $schedule->id,
                'type' => 'personal',
                'title' => $schedule->title,
                'subject' => $schedule->subject,
                'study_date' => $schedule->study_date,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'duration_minutes' => $schedule->duration_minutes,
                'url' => route('study-schedules.index'),
            ]);
        }

        $schoolSchedules = SchoolSchedule::where('user_id', $userId)
            ->get();

        foreach ($schoolSchedules as $school) {
            if (
                $school->start_date &&
                $school->end_date &&
                $school->day_of_week !== null
            ) {
} elseif ($school->study_date) {
    $schoolDate = Carbon::parse($school->study_date);

    if ($schoolDate->between(
        $today->copy()->startOfDay(),
        $endUpcomingDate->copy()->endOfDay()
    )) {
        $this->addUpcomingSchoolSchedule(
            $upcomingSchedules,
            $school,
            $schoolDate
        );
    }
}
}


        $upcomingSchedules = $upcomingSchedules
    ->sortBy(function ($schedule) {
        return Carbon::parse($schedule->study_date)->format('Y-m-d')
            . ' '
            . ($schedule->start_time ?? '00:00:00');
    })
    ->values();

        /*
        |--------------------------------------------------------------------------
        | 7. ĐỀ XUẤT AI
        |--------------------------------------------------------------------------
        */

        $aiRecommendations = AIStudyRecommendation::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('recommended_date')
            ->orderBy('recommended_start_time')
            ->take(3)
            ->get();

        $pendingRecommendationCount = AIStudyRecommendation::where(
            'user_id',
            $userId
        )->where('status', 'pending')->count();

        /*
        |--------------------------------------------------------------------------
        | 8. MỤC TIÊU HỌC TẬP
        |--------------------------------------------------------------------------
        */

        $activeGoals = LearningGoal::where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('target_date')
            ->take(4)
            ->get();

        $totalGoals = LearningGoal::where('user_id', $userId)->count();

        $completedGoals = LearningGoal::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 9. HOẠT ĐỘNG GẦN ĐÂY
        |--------------------------------------------------------------------------
        */

        $recentActivities = collect();

        $recentAttempts = ExerciseAttempt::with('exercise')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentAttempts as $attempt) {
            $recentActivities->push((object) [
                'title' => $attempt->exercise?->title ?? 'Hoàn thành câu hỏi',
                'description' => $attempt->is_correct
                    ? 'Bạn đã trả lời chính xác.'
                    : 'Bạn đã hoàn thành một lượt làm bài.',
                'icon' => '✏️',
                'created_at' => $attempt->created_at,
            ]);
        }

        $recentContents = LearningContent::where('user_id', $userId)
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($recentContents as $content) {
            $recentActivities->push((object) [
                'title' => $content->title,
                'description' => 'Nội dung học tập: ' . $content->status,
                'icon' => '📚',
                'created_at' => $content->updated_at,
            ]);
        }

        $recentSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(5)
            ->get();

        foreach ($recentSchedules as $schedule) {
            $recentActivities->push((object) [
                'title' => $schedule->title,
                'description' => 'Đã hoàn thành buổi học'
                    . ($schedule->subject ? ' môn ' . $schedule->subject : ''),
                'icon' => '✅',
                'created_at' => $schedule->updated_at,
            ]);
        }

        $recentActivities = $recentActivities
            ->sortByDesc('created_at')
            ->take(6)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 10. TRẢ DỮ LIỆU CHO DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'user',
            'totalContents',
            'completedContents',
            'contentCompletion',
            'totalSchedules',
            'completedSchedules',
            'plannedSchedules',
            'totalStudyMinutes',
            'totalStudyHours',
            'remainingMinutes',
            'totalAttempts',
            'correctAttempts',
            'accuracy',
            'studyStreak',
            'weeklyProgress',
            'maxWeeklyAttempts',
            'upcomingSchedules',
            'aiRecommendations',
            'pendingRecommendationCount',
            'activeGoals',
            'totalGoals',
            'completedGoals',
            'recentActivities'
        ));
    }

    /**
     * Thêm lịch học trên trường vào danh sách sắp tới.
     */
    private function addUpcomingSchoolSchedule(
        $collection,
        $school,
        Carbon $date
    ): void {
        $collection->push((object) [
            'id' => $school->id,
            'type' => 'school',
            'title' => $school->subject_name,
            'subject' => $school->subject_name,
            'study_date' => $date->copy(),
            'start_time' => $school->start_time,
            'end_time' => $school->end_time,
            'duration_minutes' => $school->duration_minutes ?? 0,
            'url' => route('school-schedules.index'),
        ]);
    }
}
