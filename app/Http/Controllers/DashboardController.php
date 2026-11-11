<?php

namespace App\Http\Controllers;

use App\Models\StudySchedule;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Tổng số lịch học
        $totalSchedules = StudySchedule::where('user_id', $userId)
            ->count();

        // Số buổi đã hoàn thành
        $completedSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        // Số buổi đang lên kế hoạch
        $plannedSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'planned')
            ->count();

        // Số buổi đã hủy
        $cancelledSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'cancelled')
            ->count();

        // Tổng thời gian học
        $totalStudyMinutes = StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->sum('duration_minutes');

        // Lịch học hôm nay
        $todaySchedules = StudySchedule::where('user_id', $userId)
            ->whereDate('study_date', now()->toDateString())
            ->orderBy('start_time')
            ->get();

        // Lịch học sắp tới
        $upcomingSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'planned')
            ->whereDate('study_date', '>=', now()->toDateString())
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Chuyển phút thành giờ và phút
        $totalStudyHours = intdiv((int) $totalStudyMinutes, 60);

        $remainingMinutes = (int) $totalStudyMinutes % 60;

        return view('dashboard', compact(
            'totalSchedules',
            'completedSchedules',
            'plannedSchedules',
            'cancelledSchedules',
            'totalStudyMinutes',
            'totalStudyHours',
            'remainingMinutes',
            'todaySchedules',
            'upcomingSchedules'
        ));
    }
}