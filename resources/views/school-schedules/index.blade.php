@extends('layouts.app')

@section('title', 'Lịch học ở trường')

@section('content')

<div class="container-fluid">

    {{-- Tiêu đề --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Lịch học ở trường
            </h2>

            <p class="text-muted mb-0">
                Lịch học được hiển thị theo từng tuần.
            </p>
        </div>

        <a href="{{ route('school-schedules.create') }}"
           class="btn btn-primary">

            + Thêm lịch học

        </a>

    </div>


    {{-- Thông báo thành công --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Thông báo lỗi --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Có lỗi xảy ra:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Điều hướng tuần --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <a href="{{ route(
                    'school-schedules.index',
                    ['date' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]
                ) }}"
                   class="btn btn-outline-primary">

                    ← Tuần trước

                </a>


                <div class="text-center">

                    <div class="fw-bold fs-5">

                        {{ $startOfWeek->format('d/m/Y') }}

                        -

                        {{ $endOfWeek->format('d/m/Y') }}

                    </div>

                    <div class="text-muted small">
                        Tuần học
                    </div>

                </div>


                <div class="d-flex gap-2">

                    <a href="{{ route(
                        'school-schedules.index',
                        ['date' => now()->format('Y-m-d')]
                    ) }}"
                       class="btn btn-outline-success">

                        Hôm nay

                    </a>

                    <a href="{{ route(
                        'school-schedules.index',
                        ['date' => $endOfWeek->copy()->addDay()->format('Y-m-d')]
                    ) }}"
                       class="btn btn-outline-primary">

                        Tuần sau →

                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- Bảng lịch học --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <h5 class="fw-bold mb-0">
                    Lịch học theo tuần
                </h5>

                <span class="badge bg-primary">

                    {{ $schedules->count() }} lịch học

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered align-middle mb-0 weekly-schedule-table">

                    <thead>

                        <tr class="text-center">

                            <th class="time-column">
                                Ca học
                            </th>

                            @foreach($weekDays as $day)

                                <th class="day-column">

                                    <div class="fw-bold">

                                        @switch($day->dayOfWeekIso)

                                            @case(1)
                                                Thứ 2
                                                @break

                                            @case(2)
                                                Thứ 3
                                                @break

                                            @case(3)
                                                Thứ 4
                                                @break

                                            @case(4)
                                                Thứ 5
                                                @break

                                            @case(5)
                                                Thứ 6
                                                @break

                                            @case(6)
                                                Thứ 7
                                                @break

                                            @case(7)
                                                Chủ nhật
                                                @break

                                        @endswitch

                                    </div>

                                    <div class="small">
                                        {{ $day->format('d/m/Y') }}
                                    </div>

                                </th>

                            @endforeach

                        </tr>

                    </thead>


                    <tbody>

                        {{-- Ca sáng --}}
                        <tr>

                            <td class="session-column morning">
                                <strong>Sáng</strong>
                            </td>

                            @foreach($weekDays as $day)

                                @php
                                    $daySchedules = $schedules->filter(function ($schedule) use ($day) {
                                        return $schedule->study_date->format('Y-m-d')
                                            === $day->format('Y-m-d');
                                    })->sortBy('start_time');
                                @endphp

                                <td class="schedule-cell">

                                    @foreach($daySchedules->filter(function ($schedule) {
                                        return $schedule->start_time < '12:00:00';
                                    }) as $schedule)

                                        @include(
                                            'school-schedules.partials.schedule-card',
                                            ['schedule' => $schedule]
                                        )

                                    @endforeach

                                </td>

                            @endforeach

                        </tr>


                        {{-- Ca chiều --}}
                        <tr>

                            <td class="session-column afternoon">
                                <strong>Chiều</strong>
                            </td>

                            @foreach($weekDays as $day)

                                @php
                                    $daySchedules = $schedules->filter(function ($schedule) use ($day) {
                                        return $schedule->study_date->format('Y-m-d')
                                            === $day->format('Y-m-d');
                                    })->sortBy('start_time');
                                @endphp

                                <td class="schedule-cell">

                                    @foreach($daySchedules->filter(function ($schedule) {
                                        return $schedule->start_time >= '12:00:00'
                                            && $schedule->start_time < '17:30:00';
                                    }) as $schedule)

                                        @include(
                                            'school-schedules.partials.schedule-card',
                                            ['schedule' => $schedule]
                                        )

                                    @endforeach

                                </td>

                            @endforeach

                        </tr>


                        {{-- Ca tối --}}
                        <tr>

                            <td class="session-column evening">
                                <strong>Tối</strong>
                            </td>

                            @foreach($weekDays as $day)

                                @php
                                    $daySchedules = $schedules->filter(function ($schedule) use ($day) {
                                        return $schedule->study_date->format('Y-m-d')
                                            === $day->format('Y-m-d');
                                    })->sortBy('start_time');
                                @endphp

                                <td class="schedule-cell">

                                    @foreach($daySchedules->filter(function ($schedule) {
                                        return $schedule->start_time >= '17:30:00';
                                    }) as $schedule)

                                        @include(
                                            'school-schedules.partials.schedule-card',
                                            ['schedule' => $schedule]
                                        )

                                    @endforeach

                                </td>

                            @endforeach

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- Chú thích --}}
    <div class="mt-3">

        <div class="d-flex flex-wrap gap-3 small text-muted">

            <div class="d-flex align-items-center gap-2">

                <span class="legend-box theory"></span>

                Lịch học lý thuyết

            </div>

            <div class="d-flex align-items-center gap-2">

                <span class="legend-box practice"></span>

                Lịch học thực hành

            </div>

            <div class="d-flex align-items-center gap-2">

                <span class="legend-box online"></span>

                Lịch học trực tuyến

            </div>

        </div>

    </div>

</div>

@endsection


@push('styles')

<style>

    .weekly-schedule-table {
        min-width: 1100px;
        table-layout: fixed;
    }

    .weekly-schedule-table th {
        background: #f1f5f9;
        color: #1683e8;
        vertical-align: middle;
        padding: 14px 8px;
    }

    .time-column {
        width: 95px;
        color: #1683e8;
    }

    .day-column {
        width: calc((100% - 95px) / 7);
    }

    .session-column {
        width: 95px;
        text-align: center;
        vertical-align: middle;
        font-size: 16px;
    }

    .session-column.morning {
        background: #fffed0;
        color: #7a7420;
    }

    .session-column.afternoon {
        background: #fffed0;
        color: #7a7420;
    }

    .session-column.evening {
        background: #fffed0;
        color: #7a7420;
    }

    .schedule-cell {
        min-height: 180px;
        height: 180px;
        vertical-align: top;
        padding: 8px !important;
        background-color: #ffffff;

        background-image:
            linear-gradient(#eef2f7 1px, transparent 1px),
            linear-gradient(90deg, #eef2f7 1px, transparent 1px);

        background-size: 20px 20px;
    }

    .schedule-card {
        position: relative;
        background: #e5edf5;
        border: 1px solid #b9c9d8;
        border-radius: 5px;
        padding: 9px;
        margin-bottom: 8px;
        color: #073b70;
        font-size: 12px;
        line-height: 1.5;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
    }

    .schedule-card:last-child {
        margin-bottom: 0;
    }

    .schedule-card-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #063b70;
    }

    .schedule-card-info {
        margin-bottom: 2px;
    }

    .schedule-card-actions {
        display: flex;
        gap: 5px;
        margin-top: 8px;
    }

    .schedule-card-actions .btn {
        font-size: 11px;
        padding: 3px 7px;
    }

    .legend-box {
        display: inline-block;
        width: 40px;
        height: 14px;
        border: 1px solid #b8c4ce;
    }

    .legend-box.theory {
        background: #e5edf5;
    }

    .legend-box.practice {
        background: #65c936;
    }

    .legend-box.online {
        background: #58b9ff;
    }

    @media (max-width: 768px) {

        .weekly-schedule-table {
            min-width: 1000px;
        }

        .schedule-cell {
            height: auto;
            min-height: 160px;
        }

    }

</style>

@endpush