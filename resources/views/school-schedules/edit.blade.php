@extends('layouts.app')

@section('title', 'Sửa lịch học')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Sửa lịch học</h2>
            <p class="text-muted mb-0">
                Cập nhật thông tin lịch học của bạn
            </p>
        </div>

        <a href="{{ route('school-schedules.index') }}"
           class="btn btn-outline-secondary">
            Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin lịch học</h5>
        </div>

        <div class="card-body">
            <form
                action="{{ route('school-schedules.update', $schedule->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- Tên môn học --}}
                    <div class="col-md-6 mb-3">
                        <label for="subject_name" class="form-label fw-semibold">
                            Tên môn học <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="subject_name"
                            name="subject_name"
                            class="form-control"
                            value="{{ old('subject_name', $schedule->subject_name) }}"
                            required
                        >
                    </div>

                    {{-- Mã học phần --}}
                    <div class="col-md-3 mb-3">
                        <label for="course_code" class="form-label fw-semibold">
                            Mã học phần
                        </label>

                        <input
                            type="text"
                            id="course_code"
                            name="course_code"
                            class="form-control"
                            value="{{ old('course_code', $schedule->course_code) }}"
                        >
                    </div>

                    {{-- Lớp học phần --}}
                    <div class="col-md-3 mb-3">
                        <label for="class_code" class="form-label fw-semibold">
                            Lớp học phần
                        </label>

                        <input
                            type="text"
                            id="class_code"
                            name="class_code"
                            class="form-control"
                            value="{{ old('class_code', $schedule->class_code) }}"
                        >
                    </div>

                    {{-- Phòng học --}}
                    <div class="col-md-4 mb-3">
                        <label for="room" class="form-label fw-semibold">
                            Phòng học
                        </label>

                        <input
                            type="text"
                            id="room"
                            name="room"
                            class="form-control"
                            value="{{ old('room', $schedule->room) }}"
                        >
                    </div>

                    {{-- Giảng viên --}}
                    <div class="col-md-8 mb-3">
                        <label for="instructor" class="form-label fw-semibold">
                            Giảng viên
                        </label>

                        <input
                            type="text"
                            id="instructor"
                            name="instructor"
                            class="form-control"
                            value="{{ old('instructor', $schedule->instructor) }}"
                        >
                    </div>

                    {{-- Ngày bắt đầu --}}
                    <div class="col-md-4 mb-3">
                        <label for="start_date" class="form-label fw-semibold">
                            Ngày bắt đầu <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            id="start_date"
                            name="start_date"
                            class="form-control"
                            value="{{ old(
                                'start_date',
                                $schedule->start_date
                                    ? $schedule->start_date->format('Y-m-d')
                                    : $schedule->study_date->format('Y-m-d')
                            ) }}"
                            required
                        >
                    </div>

                    {{-- Ngày kết thúc --}}
                    <div class="col-md-4 mb-3">
                        <label for="end_date" class="form-label fw-semibold">
                            Ngày kết thúc <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            id="end_date"
                            name="end_date"
                            class="form-control"
                            value="{{ old(
                                'end_date',
                                $schedule->end_date
                                    ? $schedule->end_date->format('Y-m-d')
                                    : $schedule->study_date->format('Y-m-d')
                            ) }}"
                            required
                        >
                    </div>

                    {{-- Thứ học --}}
                    <div class="col-md-4 mb-3">
                        <label for="day_of_week" class="form-label fw-semibold">
                            Thứ học <span class="text-danger">*</span>
                        </label>

                        @php
                            $currentDayOfWeek = old(
                                'day_of_week',
                                $schedule->day_of_week
                                    ?? $schedule->study_date->dayOfWeekIso
                            );
                        @endphp

                        <select
                            id="day_of_week"
                            name="day_of_week"
                            class="form-select"
                            required
                        >
                            <option value="">-- Chọn thứ --</option>

                            @for ($day = 1; $day <= 7; $day++)
                                <option
                                    value="{{ $day }}"
                                    {{ (int) $currentDayOfWeek === $day ? 'selected' : '' }}
                                >
                                    {{ $day === 7 ? 'Chủ nhật' : 'Thứ ' . ($day + 1) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Tiết bắt đầu --}}
                    <div class="col-md-4 mb-3">
                        <label for="start_period" class="form-label fw-semibold">
                            Tiết bắt đầu <span class="text-danger">*</span>
                        </label>

                        <select
                            id="start_period"
                            name="start_period"
                            class="form-select"
                            required
                        >
                            <option value="">-- Chọn tiết bắt đầu --</option>

                            @for ($period = 1; $period <= 12; $period++)
                                <option
                                    value="{{ $period }}"
                                    {{ (string) old('start_period', $startPeriod ?? '') === (string) $period ? 'selected' : '' }}
                                >
                                    Tiết {{ $period }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Tiết kết thúc --}}
                    <div class="col-md-4 mb-3">
                        <label for="end_period" class="form-label fw-semibold">
                            Tiết kết thúc <span class="text-danger">*</span>
                        </label>

                        <select
                            id="end_period"
                            name="end_period"
                            class="form-select"
                            required
                        >
                            <option value="">-- Chọn tiết kết thúc --</option>

                            @for ($period = 1; $period <= 12; $period++)
                                <option
                                    value="{{ $period }}"
                                    {{ (string) old('end_period', $endPeriod ?? '') === (string) $period ? 'selected' : '' }}
                                >
                                    Tiết {{ $period }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Khung giờ --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">
                            Khung giờ học
                        </label>

                        <div
                            id="time_preview"
                            class="alert alert-info mb-0"
                        >
                            Chọn tiết bắt đầu và tiết kết thúc để xem khung giờ.
                        </div>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        Lưu thay đổi
                    </button>

                    <a
                        href="{{ route('school-schedules.index') }}"
                        class="btn btn-light border"
                    >
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const startPeriod = document.getElementById('start_period');
    const endPeriod = document.getElementById('end_period');
    const timePreview = document.getElementById('time_preview');

    const periodTimes = {
        1: ['07:00', '07:50'],
        2: ['07:50', '08:40'],
        3: ['08:50', '09:40'],
        4: ['09:40', '10:30'],
        5: ['10:40', '11:30'],
        6: ['11:30', '12:20'],
        7: ['13:00', '13:50'],
        8: ['13:50', '14:40'],
        9: ['14:50', '15:40'],
        10: ['15:40', '16:30'],
        11: ['16:40', '17:30'],
        12: ['17:30', '18:20']
    };

    function updateTimePreview() {
        const start = startPeriod.value;
        const end = endPeriod.value;

        if (!start || !end) {
            timePreview.textContent =
                'Chọn tiết bắt đầu và tiết kết thúc để xem khung giờ.';
            return;
        }

        if (Number(start) > Number(end)) {
            timePreview.textContent =
                'Tiết bắt đầu không được lớn hơn tiết kết thúc.';
            return;
        }

        timePreview.textContent =
            'Khung giờ: ' +
            periodTimes[start][0] +
            ' - ' +
            periodTimes[end][1];
    }

    startPeriod.addEventListener('change', updateTimePreview);
    endPeriod.addEventListener('change', updateTimePreview);

    updateTimePreview();
</script>
@endpush