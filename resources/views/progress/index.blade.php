@extends('layouts.app')

@section('title', 'Tiến độ học tập')

@section('content')

<div class="container-fluid">

    {{-- Tiêu đề --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Tiến độ học tập</h2>
        <p class="text-muted mb-0">
            Theo dõi quá trình học tập và kết quả của bạn.
        </p>
    </div>


    {{-- Thống kê bài tập --}}
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Bài tập đã làm</p>
                    <h2 class="fw-bold mb-0">
                        {{ $totalAttempts }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Câu trả lời đúng</p>
                    <h2 class="fw-bold text-success mb-0">
                        {{ $correctAttempts }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Câu trả lời sai</p>
                    <h2 class="fw-bold text-danger mb-0">
                        {{ $wrongAttempts }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Độ chính xác</p>
                    <h2 class="fw-bold mb-0">
                        {{ $accuracy }}%
                    </h2>
                </div>
            </div>
        </div>

    </div>


    {{-- Điểm trung bình --}}
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        Kết quả bài tập
                    </h5>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Độ chính xác</span>
                            <strong>{{ $accuracy }}%</strong>
                        </div>

                        <div class="progress" style="height: 10px;">
                            <div
                                class="progress-bar"
                                role="progressbar"
                                style="width: {{ $accuracy }}%;"
                            ></div>
                        </div>
                    </div>

                    <div>
                        <span class="text-muted">
                            Điểm trung bình
                        </span>

                        <div class="fs-3 fw-bold mt-1">
                            {{ $averageScore }}/10
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Nội dung học tập --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Nội dung học tập
                    </h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Chưa học</span>
                        <strong>{{ $pendingContents }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Đang học</span>
                        <strong>{{ $inProgressContents }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Đã hoàn thành</span>
                        <strong class="text-success">
                            {{ $completedContents }}
                        </strong>
                    </div>

                    <div class="progress" style="height: 10px;">
                        <div
                            class="progress-bar"
                            role="progressbar"
                            style="width: {{ $contentCompletion }}%;"
                        ></div>
                    </div>

                    <small class="text-muted d-block mt-2">
                        Hoàn thành {{ $contentCompletion }}% nội dung
                    </small>

                </div>
            </div>
        </div>


        {{-- Mục tiêu --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Mục tiêu học tập
                    </h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng mục tiêu</span>
                        <strong>{{ $totalGoals }}</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Đang thực hiện</span>
                        <strong>{{ $activeGoals }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Đã hoàn thành</span>
                        <strong class="text-success">
                            {{ $completedGoals }}
                        </strong>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- Lịch tự học --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <h5 class="fw-bold mb-3">
                Lịch tự học
            </h5>

            <div class="row align-items-center">

                <div class="col-md-8">

                    <div class="d-flex justify-content-between mb-2">
                        <span>
                            Buổi học đã hoàn thành
                        </span>

                        <strong>
                            {{ $completedSchedules }}/{{ $totalSchedules }}
                        </strong>
                    </div>

                    <div class="progress" style="height: 12px;">
                        <div
                            class="progress-bar"
                            role="progressbar"
                            style="width: {{ $scheduleCompletion }}%;"
                        ></div>
                    </div>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <div class="fs-3 fw-bold">
                        {{ $scheduleCompletion }}%
                    </div>

                    <div class="text-muted">
                        Hoàn thành lịch học
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection