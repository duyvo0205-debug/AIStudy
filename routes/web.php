<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LearningGoalController;
use App\Http\Controllers\StudyScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolScheduleController;
use App\Http\Controllers\LearningContentController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\LearningPathController;
use App\Http\Controllers\RecommendationController;


/*
|--------------------------------------------------------------------------
| Trang công khai
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Auth - khách chưa đăng nhập
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Đăng nhập
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');


    /*
    |--------------------------------------------------------------------------
    | Đăng ký
    |--------------------------------------------------------------------------
    */

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.submit');

});


/*
|--------------------------------------------------------------------------
| Khu vực người dùng đã đăng nhập
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Đăng xuất
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ])->name('logout');


    /*
    |--------------------------------------------------------------------------
    | Hồ sơ cá nhân
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'index',
    ])->name('profile');

    Route::put('/profile', [
        ProfileController::class,
        'update',
    ])->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | Mục tiêu học tập
    |--------------------------------------------------------------------------
    */

    Route::get('/goals', [
        LearningGoalController::class,
        'index',
    ])->name('learning-goals.index');

    Route::get('/goals/create', [
        LearningGoalController::class,
        'create',
    ])->name('learning-goals.create');

    Route::post('/goals', [
        LearningGoalController::class,
        'store',
    ])->name('learning-goals.store');

    Route::put('/goals/{goal}/status', [
        LearningGoalController::class,
        'updateStatus',
    ])->name('learning-goals.status');

    Route::delete('/goals/{goal}', [
        LearningGoalController::class,
        'destroy',
    ])->name('learning-goals.destroy');


    /*
    |--------------------------------------------------------------------------
    | Lịch học cá nhân
    |--------------------------------------------------------------------------
    */

    Route::get('/study-schedules', [
        StudyScheduleController::class,
        'index',
    ])->name('study-schedules.index');

    Route::get('/study-schedules/create', [
        StudyScheduleController::class,
        'create',
    ])->name('study-schedules.create');

    Route::post('/study-schedules', [
        StudyScheduleController::class,
        'store',
    ])->name('study-schedules.store');

    Route::get('/study-schedules/{schedule}/edit', [
        StudyScheduleController::class,
        'edit',
    ])->name('study-schedules.edit');

    Route::put('/study-schedules/{schedule}', [
        StudyScheduleController::class,
        'update',
    ])->name('study-schedules.update');

    Route::put('/study-schedules/{schedule}/status', [
        StudyScheduleController::class,
        'updateStatus',
    ])->name('study-schedules.status');

    Route::delete('/study-schedules/{schedule}', [
        StudyScheduleController::class,
        'destroy',
    ])->name('study-schedules.destroy');


    /*
    |--------------------------------------------------------------------------
    | Lịch học ở trường
    |--------------------------------------------------------------------------
    */

    Route::get('/school-schedules', [
        SchoolScheduleController::class,
        'index',
    ])->name('school-schedules.index');

    Route::get('/school-schedules/create', [
        SchoolScheduleController::class,
        'create',
    ])->name('school-schedules.create');

    Route::post('/school-schedules', [
        SchoolScheduleController::class,
        'store',
    ])->name('school-schedules.store');

    Route::get('/school-schedules/{schedule}/edit', [
        SchoolScheduleController::class,
        'edit',
    ])->name('school-schedules.edit');

    Route::put('/school-schedules/{schedule}', [
        SchoolScheduleController::class,
        'update',
    ])->name('school-schedules.update');

    Route::delete('/school-schedules/{schedule}', [
        SchoolScheduleController::class,
        'destroy',
    ])->name('school-schedules.destroy');


    /*
    |--------------------------------------------------------------------------
    | Nội dung học tập
    |--------------------------------------------------------------------------
    */

    Route::get('/learning', [
        LearningContentController::class,
        'index',
    ])->name('learning.index');

    Route::get('/learning/create', [
        LearningContentController::class,
        'create',
    ])->name('learning.create');

    /*
    |--------------------------------------------------------------------------
    | AI tạo nội dung học tập từ lịch học đã chấp nhận
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/learning/from-schedule/{schedule}',
        [
            LearningContentController::class,
            'generateFromSchedule',
        ]
    )->name('learning.generate-from-schedule');

    Route::put('/learning/{learning}/start', [
        LearningContentController::class,
        'start',
    ])->name('learning.start');

    Route::get('/learning/{learning}', [
        LearningContentController::class,
        'show',
    ])->name('learning.show');

    Route::post('/learning', [
        LearningContentController::class,
        'store',
    ])->name('learning.store');

    Route::get('/learning/{learning}/edit', [
        LearningContentController::class,
        'edit',
    ])->name('learning.edit');

    Route::put('/learning/{learning}', [
        LearningContentController::class,
        'update',
    ])->name('learning.update');

    Route::delete('/learning/{learning}', [
        LearningContentController::class,
        'destroy',
    ])->name('learning.destroy');

    Route::put('/learning/{learning}/status', [
        LearningContentController::class,
        'updateStatus',
    ])->name('learning.status');


    /*
    |--------------------------------------------------------------------------
    | Bài tập
    |--------------------------------------------------------------------------
    */

    Route::get('/exercises', [
        ExerciseController::class,
        'index',
    ])->name('exercises.index');

    Route::get('/exercises/create', [
        ExerciseController::class,
        'create',
    ])->name('exercises.create');

    Route::post('/exercises', [
        ExerciseController::class,
        'store',
    ])->name('exercises.store');

    /*
    |--------------------------------------------------------------------------
    | AI tạo bài tập từ nội dung học tập
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/exercises/from-learning/{learning}',
        [
            ExerciseController::class,
            'generateFromLearning',
        ]
    )->name('exercises.generate-from-learning');

    Route::get('/exercises/{exercise}', [
        ExerciseController::class,
        'show',
    ])->name('exercises.show');

    Route::get('/exercises/{exercise}/edit', [
        ExerciseController::class,
        'edit',
    ])->name('exercises.edit');

    Route::put('/exercises/{exercise}', [
        ExerciseController::class,
        'update',
    ])->name('exercises.update');

    Route::delete('/exercises/{exercise}', [
        ExerciseController::class,
        'destroy',
    ])->name('exercises.destroy');

    Route::post('/exercises/{exercise}/submit', [
        ExerciseController::class,
        'submit',
    ])->name('exercises.submit');


    /*
    |--------------------------------------------------------------------------
    | Tiến độ học tập
    |--------------------------------------------------------------------------
    */

    Route::get('/progress', [
        ProgressController::class,
        'index',
    ])->name('progress.index');


    /*
    |--------------------------------------------------------------------------
    | Trợ lý AI
    |--------------------------------------------------------------------------
    */

    Route::get('/ai-assistant', [
        AIAssistantController::class,
        'index',
    ])->name('ai-assistant.index');

    Route::post('/ai-assistant/conversations', [
        AIAssistantController::class,
        'createConversation',
    ])->name('ai-assistant.conversations.create');

    Route::get('/ai-assistant/{conversation}', [
        AIAssistantController::class,
        'show',
    ])->name('ai-assistant.show');

    Route::post('/ai-assistant/{conversation}/send', [
        AIAssistantController::class,
        'send',
    ])->name('ai-assistant.send');

    Route::delete('/ai-assistant/{conversation}', [
        AIAssistantController::class,
        'destroy',
    ])->name('ai-assistant.destroy');


    /*
    |--------------------------------------------------------------------------
    | Lộ trình cá nhân
    |--------------------------------------------------------------------------
    */

    Route::get('/learning-path', [
        LearningPathController::class,
        'index',
    ])->name('learning-path.index');


    /*
    |--------------------------------------------------------------------------
    | Đề xuất học tập AI
    |--------------------------------------------------------------------------
    */

    Route::get('/recommendations', [
        RecommendationController::class,
        'index',
    ])->name('recommendations.index');

    Route::post('/recommendations/{recommendation}/accept', [
        RecommendationController::class,
        'accept',
    ])->name('recommendations.accept');

    Route::post('/recommendations/{recommendation}/reject', [
        RecommendationController::class,
        'reject',
    ])->name('recommendations.reject');

});