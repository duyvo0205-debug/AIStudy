<?php

namespace App\Http\Controllers;

use App\Models\ExerciseAttempt;
use App\Models\LearningContent;
use App\Models\LearningGoal;
use App\Models\StudySchedule;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Thống kê bài tập
        |--------------------------------------------------------------------------
        */

        $totalAttempts = ExerciseAttempt::where('user_id', $userId)
            ->count();

        $correctAttempts = ExerciseAttempt::where('user_id', $userId)
            ->where('is_correct', true)
            ->count();

        $wrongAttempts = ExerciseAttempt::where('user_id', $userId)
            ->where('is_correct', false)
            ->count();

        $averageScore = ExerciseAttempt::where('user_id', $userId)
            ->avg('score');

        $accuracy = $totalAttempts > 0
            ? round(($correctAttempts / $totalAttempts) * 100, 1)
            : 0;

        $averageScore = $averageScore !== null
            ? round($averageScore, 1)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Thống kê nội dung học tập
        |--------------------------------------------------------------------------
        */

        $totalContents = LearningContent::where('user_id', $userId)
            ->count();

        $pendingContents = LearningContent::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $inProgressContents = LearningContent::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->count();

        $completedContents = LearningContent::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $contentCompletion = $totalContents > 0
            ? round(($completedContents / $totalContents) * 100, 1)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Thống kê mục tiêu học tập
        |--------------------------------------------------------------------------
        */

        $totalGoals = LearningGoal::where('user_id', $userId)
            ->count();

        $completedGoals = LearningGoal::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $activeGoals = LearningGoal::where('user_id', $userId)
            ->where('status', 'active')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Thống kê lịch tự học
        |--------------------------------------------------------------------------
        */

        $totalSchedules = StudySchedule::where('user_id', $userId)
            ->count();

        $completedSchedules = StudySchedule::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $scheduleCompletion = $totalSchedules > 0
            ? round(($completedSchedules / $totalSchedules) * 100, 1)
            : 0;


        return view('progress.index', compact(
            'totalAttempts',
            'correctAttempts',
            'wrongAttempts',
            'averageScore',
            'accuracy',

            'totalContents',
            'pendingContents',
            'inProgressContents',
            'completedContents',
            'contentCompletion',

            'totalGoals',
            'completedGoals',
            'activeGoals',

            'totalSchedules',
            'completedSchedules',
            'scheduleCompletion'
        ));
    }
}