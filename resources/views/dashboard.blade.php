@extends('layouts.app')

@section('title', 'Tổng quan - AIStudy')

@section('content')

<div class="profile-page">

    {{-- HEADER --}}
    <div class="profile-header">

        <div>
            <div class="profile-eyebrow">
                AI STUDY DASHBOARD
            </div>

            <h1>Tổng quan học tập</h1>

            <p>
                Theo dõi kế hoạch, thời gian và tiến độ học tập của bạn.
            </p>
        </div>

        <div>
            <a
                href="{{ route('study-schedules.create') }}"
                class="profile-save-btn"
                style="text-decoration:none;"
            >
                + Thêm lịch học
            </a>
        </div>

    </div>


    {{-- THỐNG KÊ --}}
    <div class="profile-grid">

        {{-- TỔNG LỊCH HỌC --}}
        <div class="profile-card">

            <div class="card-title">
                <div class="card-icon">
                    📚
                </div>

                <div>
                    <h2>Tổng lịch học</h2>
                    <p>Tất cả buổi học</p>
                </div>
            </div>

            <div style="
                font-size:36px;
                font-weight:700;
                margin-top:20px;
            ">
                {{ $totalSchedules }}
            </div>

            <p style="color:var(--muted);">
                buổi học
            </p>

        </div>


        {{-- ĐÃ HOÀN THÀNH --}}
        <div class="profile-card">

            <div class="card-title">
                <div class="card-icon">
                    ✅
                </div>

                <div>
                    <h2>Đã hoàn thành</h2>
                    <p>Buổi học hoàn tất</p>
                </div>
            </div>

            <div style="
                font-size:36px;
                font-weight:700;
                color:#2e9b63;
                margin-top:20px;
            ">
                {{ $completedSchedules }}
            </div>

            <p style="color:var(--muted);">
                buổi học
            </p>

        </div>


        {{-- ĐANG LÊN KẾ HOẠCH --}}
        <div class="profile-card">

            <div class="card-title">
                <div class="card-icon">
                    📝
                </div>

                <div>
                    <h2>Đang lên kế hoạch</h2>
                    <p>Buổi học sắp tới</p>
                </div>
            </div>

            <div style="
                font-size:36px;
                font-weight:700;
                color:#d99a00;
                margin-top:20px;
            ">
                {{ $plannedSchedules }}
            </div>

            <p style="color:var(--muted);">
                buổi học
            </p>

        </div>


        {{-- TỔNG THỜI GIAN --}}
        <div class="profile-card">

            <div class="card-title">
                <div class="card-icon">
                    ⏱️
                </div>

                <div>
                    <h2>Thời gian học</h2>
                    <p>Đã hoàn thành</p>
                </div>
            </div>

            <div style="
                font-size:36px;
                font-weight:700;
                margin-top:20px;
            ">
                {{ $totalStudyHours }}
            </div>

            <p style="color:var(--muted);">
                giờ {{ $remainingMinutes }} phút
            </p>

        </div>

    </div>


    {{-- LỊCH HỌC HÔM NAY --}}
    <div class="profile-card" style="margin-top:25px;">

        <div class="card-title">

            <div class="card-icon">
                📅
            </div>

            <div>
                <h2>Lịch học hôm nay</h2>

                <p>
                    {{ now()->format('d/m/Y') }}
                </p>
            </div>

        </div>


        @if($todaySchedules->count() === 0)

            <div style="
                text-align:center;
                padding:30px 10px;
                color:var(--muted);
            ">
                Hôm nay bạn chưa có lịch học.
            </div>

        @else

            @foreach($todaySchedules as $schedule)

                <div style="
                    padding:18px 0;
                    border-bottom:1px solid var(--border);
                ">

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        gap:15px;
                        flex-wrap:wrap;
                    ">

                        <div>

                            <h3 style="margin-bottom:5px;">
                                {{ $schedule->title }}
                            </h3>

                            @if($schedule->subject)

                                <p style="
                                    margin:0;
                                    color:var(--muted);
                                ">
                                    Môn học: {{ $schedule->subject }}
                                </p>

                            @endif

                        </div>

                        <div style="font-weight:600;">
                            {{ substr($schedule->start_time, 0, 5) }}
                            -
                            {{ substr($schedule->end_time, 0, 5) }}
                        </div>

                    </div>

                    <p style="
                        margin:8px 0 0;
                        color:var(--muted);
                        font-size:13px;
                    ">
                        Thời lượng:
                        {{ $schedule->duration_minutes }} phút
                    </p>

                    @if($schedule->status === 'completed')

                        <span style="color:#2e9b63;">
                            ✓ Đã hoàn thành
                        </span>

                    @elseif($schedule->status === 'cancelled')

                        <span style="color:#e5484d;">
                            ✕ Đã hủy
                        </span>

                    @else

                        <span style="color:#d99a00;">
                            ⏳ Đã lên kế hoạch
                        </span>

                    @endif

                </div>

            @endforeach

        @endif

    </div>


    {{-- LỊCH HỌC SẮP TỚI --}}
    <div class="profile-card" style="margin-top:25px;">

        <div class="card-title">

            <div class="card-icon">
                🗓️
            </div>

            <div>
                <h2>Lịch học sắp tới</h2>

                <p>
                    Những buổi học tiếp theo của bạn
                </p>
            </div>

        </div>


        @if($upcomingSchedules->count() === 0)

            <div style="
                text-align:center;
                padding:30px 10px;
                color:var(--muted);
            ">
                Chưa có lịch học sắp tới.
            </div>

        @else

            @foreach($upcomingSchedules as $schedule)

                <div style="
                    padding:18px 0;
                    border-bottom:1px solid var(--border);
                ">

                    <div style="
                        display:flex;
                        justify-content:space-between;
                        gap:15px;
                        flex-wrap:wrap;
                    ">

                        <div>

                            <h3 style="margin-bottom:5px;">
                                {{ $schedule->title }}
                            </h3>

                            @if($schedule->subject)

                                <p style="
                                    margin:0;
                                    color:var(--muted);
                                ">
                                    {{ $schedule->subject }}
                                </p>

                            @endif

                        </div>

                        <div style="font-weight:600;">
                            {{ $schedule->study_date->format('d/m/Y') }}
                        </div>

                    </div>

                    <p style="
                        margin:8px 0 0;
                        color:var(--muted);
                        font-size:13px;
                    ">
                        Thời gian:
                        {{ substr($schedule->start_time, 0, 5) }}
                        -
                        {{ substr($schedule->end_time, 0, 5) }}
                    </p>

                    @if($schedule->priority === 'high')

                        <span style="color:#e5484d;">
                            🔴 Ưu tiên cao
                        </span>

                    @elseif($schedule->priority === 'medium')

                        <span style="color:#d99a00;">
                            🟡 Ưu tiên trung bình
                        </span>

                    @else

                        <span style="color:#2e9b63;">
                            🟢 Ưu tiên thấp
                        </span>

                    @endif

                </div>

            @endforeach

        @endif


        <div style="margin-top:20px;">

            <a
                href="{{ route('study-schedules.index') }}"
                class="profile-save-btn"
                style="text-decoration:none;"
            >
                Xem tất cả lịch học
            </a>

        </div>

    </div>


    {{-- NÚT TỚI CÁC CHỨC NĂNG --}}
    <div
        style="
            display:flex;
            gap:15px;
            flex-wrap:wrap;
            margin-top:25px;
        "
    >

        <a
            href="{{ route('profile') }}"
            class="profile-save-btn"
            style="text-decoration:none;"
        >
            Hồ sơ cá nhân
        </a>

        <a
            href="{{ route('study-schedules.create') }}"
            class="profile-save-btn"
            style="text-decoration:none;"
        >
            Tạo kế hoạch học
        </a>

    </div>

</div>

@endsection