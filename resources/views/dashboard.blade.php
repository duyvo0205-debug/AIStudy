
@extends('layouts.app')

@section('title', 'Tổng quan - AIStudy')

@section('content')

<style>
.dashboard-page {
    --dash-primary: #6757f5;
    --dash-primary-dark: #5545e8;
    --dash-text: #20243a;
    --dash-muted: #858ba1;
    --dash-border: #ececf4;
    --dash-surface: #ffffff;
    width: 100%;
    max-width: 1600px;
    margin: auto;
    padding: 25px 28px 40px;
    color: var(--dash-text);
}

.dashboard-page * {
    box-sizing: border-box;
}

.dashboard-page a {
    text-decoration: none;
}


    
/* BANNER AIStudy */

.dashboard-banner {
    position: relative;
    display: flex;
    align-items: center;
    min-height: 250px;
    margin-bottom: 24px;
    padding: 32px 48px;
    overflow: hidden;
    border-radius: 23px;

    background-image:
        linear-gradient(
            90deg,
            rgba(69, 65, 225, .18) 0%,
            rgba(69, 65, 225, .08) 48%,
            rgba(69, 65, 225, 0) 75%
        ),
        url('/images/aistudy-banner-bg.png');

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    box-shadow: 0 12px 30px rgba(103, 87, 245, .16);
}

.dashboard-banner::before,
.dashboard-banner::after {
    display: none;
}

.banner-content {
    position: relative;
    z-index: 2;
    width: 58%;
    max-width: 700px;
}

.banner-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 15px;
    padding: 8px 16px;
    border-radius: 30px;
    background: rgba(255, 255, 255, .22);
    border: 1px solid rgba(255, 255, 255, .2);
    color: white;
    font-size: 13px;
    font-weight: 700;
    backdrop-filter: blur(8px);
}

.banner-title {
    margin: 0;
    color: #fff;
    font-size: clamp(27px, 3.3vw, 43px);
    font-weight: 800;
    line-height: 1.22;
    letter-spacing: -.8px;
    text-shadow: 0 2px 12px rgba(43, 40, 150, .12);
}

.banner-title span {
    background: linear-gradient(
        90deg,
        #ffffff 0%,
        #e6dcff 48%,
        #c9b8ff 100%
    );
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.banner-description {
    max-width: 540px;
    margin: 15px 0 21px;
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    line-height: 1.6;
    text-shadow: 0 2px 8px rgba(43, 40, 150, .12);
}

.banner-button {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 12px 21px;
    border-radius: 13px;
    background: linear-gradient(135deg, #7138f5, #5424df);
    color: white;
    font-size: 14px;
    font-weight: 750;
    box-shadow: 0 6px 16px rgba(58, 29, 190, .25);
    transition: .2s;
}

.banner-button:hover {
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 9px 20px rgba(58, 29, 190, .32);
}

@media (max-width: 700px) {
    .dashboard-banner {
        min-height: 230px;
        padding: 25px 22px;
        background-position: 58% center;
    }

    .banner-content {
        width: 75%;
    }

    .banner-title {
        font-size: 27px;
    }

    .banner-description {
        font-size: 13px;
    }
}

@media (max-width: 450px) {
    .dashboard-banner {
        background-position: 62% center;
    }

    .banner-content {
        width: 90%;
    }

    .banner-title {
        font-size: 23px;
    }
}


.dashboard-banner::before,
.dashboard-banner::after {
    position: absolute;
    border-radius: 50%;
    content: "";
    background: rgba(255,255,255,.09);
}

.dashboard-banner::before {
    width: 280px;
    height: 280px;
    top: -150px;
    right: 200px;
}

.dashboard-banner::after {
    width: 300px;
    height: 300px;
    bottom: -200px;
    right: -50px;
}

.banner-content {
    position: relative;
    z-index: 2;
    max-width: 720px;
}

.banner-label {
    display: inline-block;
    margin-bottom: 12px;
    padding: 7px 13px;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 30px;
    background: rgba(255,255,255,.13);
    color: white;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .7px;
}

.banner-title {
    margin: 0;
    color: white;
    font-size: clamp(25px, 3vw, 35px);
    font-weight: 800;
    line-height: 1.3;
}

.banner-description {
    max-width: 600px;
    margin: 12px 0 20px;
    color: rgba(255,255,255,.88);
    font-size: 14px;
    line-height: 1.7;
}

.banner-button {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 12px 19px;
    border-radius: 11px;
    background: white;
    color: #5d4cf0;
    font-size: 13px;
    font-weight: 750;
    transition: .2s;
}

.banner-button:hover {
    color: #5545e8;
    transform: translateY(-2px);
    box-shadow: 0 7px 18px rgba(30,20,100,.18);
}

.banner-visual {
    position: relative;
    z-index: 2;
    display: flex;
    flex: 0 0 175px;
    align-items: center;
    justify-content: center;
    width: 175px;
    height: 155px;
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 28px;
    background: rgba(255,255,255,.13);
    font-size: 85px;
}

/* Statistics */

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 17px;
    margin-bottom: 23px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    padding: 21px 18px;
    border: 1px solid var(--dash-border);
    border-radius: 17px;
    background: var(--dash-surface);
    box-shadow: 0 5px 20px rgba(35,48,86,.035);
    transition: .2s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(35,48,86,.07);
}

.stat-icon {
    display: flex;
    flex: 0 0 49px;
    align-items: center;
    justify-content: center;
    width: 49px;
    height: 49px;
    border-radius: 15px;
    background: #f0edff;
    font-size: 23px;
}

.stat-icon.green { background: #e8f8ef; }
.stat-icon.orange { background: #fff2e7; }
.stat-icon.blue { background: #eaf3ff; }

.stat-info {
    min-width: 0;
}

.stat-title {
    margin: 0 0 5px;
    color: var(--dash-muted);
    font-size: 12px;
    font-weight: 600;
}

.stat-value {
    margin: 0;
    color: var(--dash-text);
    font-size: 25px;
    font-weight: 800;
    line-height: 1.2;
}

.stat-unit {
    color: var(--dash-muted);
    font-size: 11px;
}

/* Main grids */

.dashboard-grid-three {
    display: grid;
    grid-template-columns: 1.05fr 1fr 1fr;
    gap: 18px;
    align-items: stretch;
    margin-bottom: 20px;
}

.dashboard-grid-two {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 18px;
}

.dashboard-panel {
    min-width: 0;
    padding: 22px;
    border: 1px solid var(--dash-border);
    border-radius: 19px;
    background: var(--dash-surface);
    box-shadow: 0 5px 20px rgba(35,48,86,.035);
}

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 20px;
}

.panel-heading {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
}

.panel-icon {
    display: flex;
    flex: 0 0 42px;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 13px;
    background: #f0edff;
    font-size: 20px;
}

.panel-title {
    margin: 0;
    color: var(--dash-text);
    font-size: 15px;
    font-weight: 800;
}

.panel-subtitle {
    margin: 4px 0 0;
    color: var(--dash-muted);
    font-size: 11px;
}

.panel-link {
    color: var(--dash-primary);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.panel-link:hover {
    color: var(--dash-primary-dark);
}

.panel-empty {
    padding: 25px 12px;
    border: 1px dashed var(--dash-border);
    border-radius: 13px;
    color: var(--dash-muted);
    text-align: center;
    font-size: 12px;
}

/* Progress */

.progress-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 19px;
}

.progress-percent {
    color: var(--dash-primary);
    font-size: 29px;
    font-weight: 800;
}

.progress-caption {
    color: var(--dash-muted);
    font-size: 11px;
}

.progress-track {
    height: 9px;
    overflow: hidden;
    border-radius: 20px;
    background: #efedf9;
}

.progress-fill {
    height: 100%;
    border-radius: 20px;
    background: linear-gradient(90deg, #6757f5, #a093ff);
}

.week-chart {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    gap: 8px;
    height: 125px;
    margin-top: 22px;
    padding-top: 8px;
    border-bottom: 1px solid var(--dash-border);
}

.week-column {
    display: flex;
    flex: 1;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: 7px;
    height: 100%;
}

.week-bar-wrap {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    width: 100%;
    height: 95px;
}

.week-bar {
    width: min(25px, 75%);
    min-height: 3px;
    border-radius: 7px 7px 2px 2px;
    background: linear-gradient(180deg, #a496ff, #6757f5);
}

.week-label {
    color: var(--dash-muted);
    font-size: 10px;
}

.chart-note {
    margin-top: 11px;
    color: var(--dash-muted);
    font-size: 11px;
}

/* Upcoming schedules */

.schedule-list,
.recommendation-list,
.activity-list,
.goal-list {
    display: flex;
    flex-direction: column;
}

.schedule-item {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f5;
}

.schedule-item:last-child,
.recommendation-item:last-child,
.activity-item:last-child,
.goal-item:last-child {
    border-bottom: 0;
}

.schedule-date {
    flex: 0 0 47px;
    padding: 8px 4px;
    border-radius: 10px;
    background: #f1efff;
    color: var(--dash-primary);
    text-align: center;
    font-size: 11px;
    font-weight: 800;
}

.schedule-date small {
    display: block;
    margin-top: 3px;
    font-size: 9px;
    font-weight: 600;
}

.schedule-detail {
    flex: 1;
    min-width: 0;
}

.item-title {
    margin: 0 0 5px;
    color: var(--dash-text);
    font-size: 12px;
    font-weight: 750;
    overflow-wrap: anywhere;
}

.item-meta {
    margin: 0;
    color: var(--dash-muted);
    font-size: 10px;
    line-height: 1.6;
}

/* AI recommendation */

.recommendation-item {
    padding: 13px 0;
    border-bottom: 1px solid #f0f0f5;
}

.recommendation-tag {
    display: inline-block;
    margin-bottom: 7px;
    padding: 4px 8px;
    border-radius: 20px;
    background: #f0edff;
    color: var(--dash-primary);
    font-size: 9px;
    font-weight: 750;
}

.recommendation-title {
    margin: 0 0 6px;
    color: var(--dash-text);
    font-size: 12px;
    font-weight: 750;
    line-height: 1.5;
}

.recommendation-description {
    margin: 0;
    color: var(--dash-muted);
    font-size: 10px;
    line-height: 1.6;
}

.recommendation-time {
    margin-top: 7px;
    color: var(--dash-primary);
    font-size: 10px;
    font-weight: 700;
}

/* Recent activity */

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 13px 0;
    border-bottom: 1px solid #f0f0f5;
}

.activity-icon {
    display: flex;
    flex: 0 0 37px;
    align-items: center;
    justify-content: center;
    width: 37px;
    height: 37px;
    border-radius: 12px;
    background: #f1efff;
    font-size: 17px;
}

.activity-detail {
    flex: 1;
    min-width: 0;
}

.activity-time {
    margin-top: 5px;
    color: var(--dash-muted);
    font-size: 10px;
}

/* Goals */

.goal-item {
    padding: 14px 0;
    border-bottom: 1px solid #f0f0f5;
}

.goal-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}

.goal-status {
    flex: 0 0 auto;
    padding: 5px 8px;
    border-radius: 20px;
    background: #eaf8f0;
    color: #218653;
    font-size: 9px;
    font-weight: 700;
}

.goal-level {
    margin: 7px 0;
    color: var(--dash-muted);
    font-size: 10px;
}

.goal-level strong {
    color: var(--dash-primary);
}

.goal-date {
    margin-top: 7px;
    color: var(--dash-muted);
    font-size: 10px;
}

/* Footer */

.dashboard-footer {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 21px;
}

.dashboard-footer a {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 11px 15px;
    border: 1px solid #e2defe;
    border-radius: 11px;
    background: #f8f7ff;
    color: var(--dash-primary-dark);
    font-size: 12px;
    font-weight: 700;
}

.dashboard-footer a:hover {
    background: #efecff;
}

/* Responsive */

@media (max-width: 1100px) {
    .dashboard-grid-three {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 700px) {
    .dashboard-page {
        padding: 18px 13px 30px;
    }

    .dashboard-banner {
        min-height: auto;
        padding: 25px 22px;
    }

    .banner-visual {
        display: none;
    }

    .banner-title {
        font-size: 25px;
    }

    .dashboard-grid-three,
    .dashboard-grid-two {
        grid-template-columns: 1fr;
    }

    .dashboard-panel {
        padding: 18px;
    }
}

@media (max-width: 450px) {
    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 16px;
    }

    .dashboard-footer a {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="dashboard-page">

    {{-- BANNER --}}
    
<section class="dashboard-banner">
    <div class="banner-content">

        <div class="banner-label">
            ✨ Chào mừng trở lại!
        </div>

        <h1 class="banner-title">
            Học thông minh hơn
            <br>
            cùng <span>AIStudy</span>
        </h1>

        <p class="banner-description">
            Cá nhân hóa lộ trình học tập,
            chinh phục mục tiêu của bạn!
        </p>

        <a href="{{ route('study-schedules.create') }}"
           class="banner-button">
            🚀 Khám phá ngay <span>→</span>
        </a>

    </div>
</section>


    {{-- THỐNG KÊ --}}
    <section class="dashboard-stats">

        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-info">
                <p class="stat-title">Tổng nội dung học</p>
                <p class="stat-value">{{ $totalContents }}</p>
                <span class="stat-unit">Nội dung học tập</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div class="stat-info">
                <p class="stat-title">Tỷ lệ hoàn thành</p>
                <p class="stat-value">{{ $contentCompletion }}%</p>
                <span class="stat-unit">
                    {{ $completedContents }}/{{ $totalContents }} nội dung
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">⏱️</div>
            <div class="stat-info">
                <p class="stat-title">Thời gian học</p>
                <p class="stat-value">
                    {{ $totalStudyHours }}h
                    @if($remainingMinutes > 0)
                        {{ $remainingMinutes }}p
                    @endif
                </p>
                <span class="stat-unit">Từ lịch học đã hoàn thành</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">🔥</div>
            <div class="stat-info">
                <p class="stat-title">Chuỗi ngày học</p>
                <p class="stat-value">{{ $studyStreak }}</p>
                <span class="stat-unit">Ngày liên tiếp</span>
            </div>
        </div>

    </section>

    {{-- HÀNG 1: 3 CỘT --}}
    <section class="dashboard-grid-three">

        {{-- TIẾN ĐỘ HỌC TẬP --}}
        <div class="dashboard-panel">

            <div class="panel-header">
                <div class="panel-heading">
                    <div class="panel-icon">📊</div>
                    <div>
                        <h2 class="panel-title">Tiến độ học tập</h2>
                        <p class="panel-subtitle">Kết quả làm bài tập</p>
                    </div>
                </div>

                <a href="{{ route('progress.index') }}" class="panel-link">
                    Chi tiết →
                </a>
            </div>

            <div class="progress-summary">
                <div>
                    <div class="progress-percent">{{ $accuracy }}%</div>
                    <div class="progress-caption">Tỷ lệ trả lời đúng</div>
                </div>

                <div style="text-align:right">
                    <strong>{{ $correctAttempts }}/{{ $totalAttempts }}</strong>
                    <div class="progress-caption">Câu đúng</div>
                </div>
            </div>

            <div class="progress-track">
                <div class="progress-fill"
                     style="width: {{ min(100, max(0, $accuracy)) }}%">
                </div>
            </div>

            <div class="week-chart">
                @foreach($weeklyProgress as $day)
                    <div class="week-column">
                        <div class="week-bar-wrap">
                            <div class="week-bar"
                                 title="{{ $day['attempts'] }} lượt làm bài"
                                 style="height: {{ $day['attempts'] > 0 ? max(7, ($day['attempts'] / $maxWeeklyAttempts) * 100) : 3 }}%">
                            </div>
                        </div>
                        <span class="week-label">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <p class="chart-note">
                Số lượt làm bài trong 7 ngày gần nhất.
            </p>
        </div>

        {{-- LỊCH HỌC SẮP TỚI --}}
        <div class="dashboard-panel">

            <div class="panel-header">
                <div class="panel-heading">
                    <div class="panel-icon">📅</div>
                    <div>
                        <h2 class="panel-title">Lịch học sắp tới</h2>
                        <p class="panel-subtitle">Trong 7 ngày tiếp theo</p>
                    </div>
                </div>

                <a href="{{ route('study-schedules.index') }}"
                   class="panel-link">
                    Tất cả →
                </a>
            </div>

            @if($upcomingSchedules->isEmpty())
                <div class="panel-empty">
                    Bạn chưa có lịch học sắp tới.
                </div>
            @else
                <div class="schedule-list">
                    @foreach($upcomingSchedules as $schedule)
                        <div class="schedule-item">

                            @php
    $scheduleDate = \Carbon\Carbon::parse($schedule->study_date)->startOfDay();
    $dayOffset = (int) \Carbon\Carbon::today()->diffInDays($scheduleDate, false);

    $dayLabel = match ($dayOffset) {
        0 => 'Hôm nay',
        1 => 'Ngày mai',
        2 => 'Ngày mốt',
        default => $scheduleDate->format('d/m'),
    };
@endphp

<div class="schedule-date">
    {{ $scheduleDate->format('d') }}
    <small>{{ $dayLabel }}</small>
</div>

                            <div class="schedule-detail">
                                <h3 class="item-title">
                                    {{ $schedule->title }}
                                </h3>

                                <p class="item-meta">
                                    {{ $schedule->subject ?: 'Chưa phân loại môn học' }}
                                </p>

                                <p class="item-meta">
                                    {{ substr($schedule->start_time, 0, 5) }}
                                    –
                                    {{ substr($schedule->end_time, 0, 5) }}
                                    · {{ $schedule->duration_minutes }} phút
                                </p>

                                <p class="item-meta">
                                    {{ $schedule->type === 'school'
                                        ? 'Thời khóa biểu trên trường'
                                        : 'Lịch học cá nhân' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ĐỀ XUẤT AI --}}
        <div class="dashboard-panel dashboard-panel-ai">

            <div class="panel-header">
                <div class="panel-heading">
                    <div class="panel-icon">✨</div>
                    <div>
                        <h2 class="panel-title">Đề xuất từ AI</h2>
                        <p class="panel-subtitle">
                            {{ $pendingRecommendationCount }} đề xuất đang chờ
                        </p>
                    </div>
                </div>

                <a href="{{ route('recommendations.index') }}"
                   class="panel-link">
                    Xem →
                </a>
            </div>

            @if($aiRecommendations->isEmpty())
                <div class="panel-empty">
                    Chưa có đề xuất mới từ AIStudy.
                </div>
            @else
                <div class="recommendation-list">
                    @foreach($aiRecommendations as $recommendation)
                        <div class="recommendation-item">

                            <span class="recommendation-tag">
                                {{ $recommendation->subject ?: 'Gợi ý học tập' }}
                            </span>

                            <h3 class="recommendation-title">
                                {{ $recommendation->title }}
                            </h3>

                            <p class="recommendation-description">
                                {{ \Illuminate\Support\Str::limit(
                                    $recommendation->reason
                                        ?: $recommendation->description,
                                    105
                                ) }}
                            </p>

                            <div class="recommendation-time">
    📅
    {{ $recommendation->recommended_date
        ? \Carbon\Carbon::parse($recommendation->recommended_date)->format('d/m/Y')
        : 'Chưa xác định ngày' }}
</div>

<a href="{{ route('recommendations.index') }}"
   class="ai-start-button">
    ▶ Bắt đầu học ngay
</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </section>

    {{-- HÀNG 2: 2 CỘT --}}
    <section class="dashboard-grid-two">

        {{-- HOẠT ĐỘNG GẦN ĐÂY --}}
        <div class="dashboard-panel">

            <div class="panel-header">
                <div class="panel-heading">
                    <div class="panel-icon">🕒</div>
                    <div>
                        <h2 class="panel-title">Hoạt động gần đây</h2>
                        <p class="panel-subtitle">Những hoạt động học tập mới nhất</p>
                    </div>
                </div>
            </div>

            @if($recentActivities->isEmpty())
                <div class="panel-empty">
                    Chưa có hoạt động học tập nào được ghi nhận.
                </div>
            @else
                <div class="activity-list">
                    @foreach($recentActivities as $activity)
                        <div class="activity-item">

                            <div class="activity-icon">
                                {{ $activity->icon }}
                            </div>

                            <div class="activity-detail">
                                <h3 class="item-title">
                                    {{ $activity->title }}
                                </h3>

                                <p class="item-meta">
                                    {{ $activity->description }}
                                </p>

                                <div class="activity-time">
                                    {{ $activity->created_at
                                        ? \Carbon\Carbon::parse($activity->created_at)->diffForHumans()
                                        : '' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- MỤC TIÊU HỌC TẬP --}}
        <div class="dashboard-panel">

            <div class="panel-header">
                <div class="panel-heading">
                    <div class="panel-icon">🎯</div>
                    <div>
                        <h2 class="panel-title">Mục tiêu học tập</h2>
                        <p class="panel-subtitle">
                            {{ $completedGoals }}/{{ $totalGoals }} mục tiêu đã hoàn thành
                        </p>
                    </div>
                </div>

                <a href="{{ route('learning-goals.index') }}" class="panel-link">
                    Quản lý →
                </a>
            </div>

            @if($activeGoals->isEmpty())
                <div class="panel-empty">
                    Bạn chưa có mục tiêu học tập đang thực hiện.
                    <br><br>
                    <a href="{{ route('learning-goals.create') }}"
                       class="panel-link">
                        + Tạo mục tiêu mới
                    </a>
                </div>
            @else
                <div class="goal-list">
                    @foreach($activeGoals as $goal)
                        <div class="goal-item">

                            <div class="goal-top">
                                <h3 class="item-title">
                                    {{ $goal->title }}
                                </h3>

                                <span class="goal-status">Đang thực hiện</span>
                            </div>

                            <p class="goal-level">
                                Môn học:
                                <strong>{{ $goal->subject ?: 'Chưa xác định' }}</strong>
                            </p>

                            <p class="goal-level">
                                Cấp độ:
                                <strong>
                                    {{ [
                                        'beginner' => 'Cơ bản',
                                        'intermediate' => 'Trung cấp',
                                        'advanced' => 'Nâng cao'
                                    ][$goal->current_level] ?? $goal->current_level }}
                                </strong>
                                →
                                <strong>
                                    {{ [
                                        'beginner' => 'Cơ bản',
                                        'intermediate' => 'Trung cấp',
                                        'advanced' => 'Nâng cao'
                                    ][$goal->target_level] ?? $goal->target_level }}
                                </strong>
                            </p>

                            @if($goal->target_date)
                                <p class="goal-date">
                                    🎯 Hạn hoàn thành:
                                    {{ $goal->target_date->format('d/m/Y') }}
                                </p>
                            @endif

                            <p class="goal-date">
                                ⏱️ Mục tiêu học:
                                {{ $goal->daily_minutes }} phút/ngày
                            </p>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </section>

    {{-- LIÊN KẾT NHANH --}}
    <div class="dashboard-footer">
        <a href="{{ route('study-schedules.create') }}">
            ＋ Tạo kế hoạch học
        </a>

        <a href="{{ route('learning-goals.create') }}">
            🎯 Tạo mục tiêu
        </a>

        <a href="{{ route('progress.index') }}">
            📊 Xem tiến độ
        </a>

        <a href="{{ route('recommendations.index') }}">
            ✨ Đề xuất AI
        </a>
    </div>

</div>

@endsection
