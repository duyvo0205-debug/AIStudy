@extends('layouts.app')

@section('title', 'Thêm lịch học ở trường')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <a href="{{ route('school-schedules.index') }}"
           class="text-decoration-none text-muted">

            ← Quay lại lịch học

        </a>

        <h2 class="fw-bold mt-3 mb-1">
            Thêm lịch học ở trường
        </h2>

        <p class="text-muted mb-0">
            Chỉ cần nhập một lần, lịch sẽ tự động xuất hiện
            trong tất cả các tuần thuộc khoảng thời gian đã chọn.
        </p>

    </div>

    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Vui lòng kiểm tra lại:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form action="{{ route('school-schedules.store') }}"
                  method="POST">

                @csrf

                <h5 class="fw-bold mb-3">
                    🏫 Thông tin môn học
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label for="subject_name"
                               class="form-label fw-semibold">

                            Tên môn học
                            <span class="text-danger">*</span>

                        </label>

                        <input type="text"
                               id="subject_name"
                               name="subject_name"
                               value="{{ old('subject_name') }}"
                               class="form-control"
                               placeholder="Ví dụ: Lập trình Web"
                               required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="course_code"
                               class="form-label fw-semibold">

                            Mã học phần

                        </label>

                        <input type="text"
                               id="course_code"
                               name="course_code"
                               value="{{ old('course_code') }}"
                               class="form-control"
                               placeholder="Ví dụ: WEB101">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="class_code"
                               class="form-label fw-semibold">

                            Lớp học phần

                        </label>

                        <input type="text"
                               id="class_code"
                               name="class_code"
                               value="{{ old('class_code') }}"
                               class="form-control"
                               placeholder="Ví dụ: 23DTH01">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="room"
                               class="form-label fw-semibold">

                            Phòng học

                        </label>

                        <input type="text"
                               id="room"
                               name="room"
                               value="{{ old('room') }}"
                               class="form-control"
                               placeholder="Ví dụ: A101">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="instructor"
                               class="form-label fw-semibold">

                            Giảng viên

                        </label>

                        <input type="text"
                               id="instructor"
                               name="instructor"
                               value="{{ old('instructor') }}"
                               class="form-control"
                               placeholder="Ví dụ: Nguyễn Văn A">

                    </div>

                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-2">
                    📅 Thời gian lặp lại
                </h5>

                <p class="text-muted small">
                    Lịch sẽ xuất hiện mỗi tuần vào đúng thứ bạn chọn,
                    từ ngày bắt đầu đến ngày kết thúc.
                </p>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label for="start_date"
                               class="form-label fw-semibold">

                            Ngày bắt đầu
                            <span class="text-danger">*</span>

                        </label>

                        <input type="date"
                               id="start_date"
                               name="start_date"
                               value="{{ old('start_date') }}"
                               class="form-control"
                               required>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label for="end_date"
                               class="form-label fw-semibold">

                            Ngày kết thúc
                            <span class="text-danger">*</span>

                        </label>

                        <input type="date"
                               id="end_date"
                               name="end_date"
                               value="{{ old('end_date') }}"
                               class="form-control"
                               required>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label for="day_of_week"
                               class="form-label fw-semibold">

                            Thứ học
                            <span class="text-danger">*</span>

                        </label>

                        <select id="day_of_week"
                                name="day_of_week"
                                class="form-select"
                                required>

                            <option value="">
                                -- Chọn thứ --
                            </option>

                            <option value="1"
                                {{ old('day_of_week') == 1 ? 'selected' : '' }}>
                                Thứ 2
                            </option>

                            <option value="2"
                                {{ old('day_of_week') == 2 ? 'selected' : '' }}>
                                Thứ 3
                            </option>

                            <option value="3"
                                {{ old('day_of_week') == 3 ? 'selected' : '' }}>
                                Thứ 4
                            </option>

                            <option value="4"
                                {{ old('day_of_week') == 4 ? 'selected' : '' }}>
                                Thứ 5
                            </option>

                            <option value="5"
                                {{ old('day_of_week') == 5 ? 'selected' : '' }}>
                                Thứ 6
                            </option>

                            <option value="6"
                                {{ old('day_of_week') == 6 ? 'selected' : '' }}>
                                Thứ 7
                            </option>

                            <option value="7"
                                {{ old('day_of_week') == 7 ? 'selected' : '' }}>
                                Chủ nhật
                            </option>

                        </select>

                    </div>

                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-2">
                    🕐 Tiết học
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label for="start_period"
                               class="form-label fw-semibold">

                            Tiết bắt đầu
                            <span class="text-danger">*</span>

                        </label>

                        <select id="start_period"
                                name="start_period"
                                class="form-select"
                                required>

                            <option value="">
                                -- Chọn tiết bắt đầu --
                            </option>

                            @for($period = 1; $period <= 13; $period++)

                                <option value="{{ $period }}"
                                    {{ old('start_period') == $period ? 'selected' : '' }}>

                                    Tiết {{ $period }}

                                </option>

                            @endfor

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label for="end_period"
                               class="form-label fw-semibold">

                            Tiết kết thúc
                            <span class="text-danger">*</span>

                        </label>

                        <select id="end_period"
                                name="end_period"
                                class="form-select"
                                required>

                            <option value="">
                                -- Chọn tiết kết thúc --
                            </option>

                            @for($period = 1; $period <= 13; $period++)

                                <option value="{{ $period }}"
                                    {{ old('end_period') == $period ? 'selected' : '' }}>

                                    Tiết {{ $period }}

                                </option>

                            @endfor

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-semibold">
                            Khung giờ tự động
                        </label>

                        <div id="period-time-preview"
                             class="form-control bg-light">

                            Chọn tiết để xem giờ

                        </div>

                    </div>

                </div>

                <div class="alert alert-info">

                    <strong>Ví dụ:</strong>

                    Nếu chọn Thứ 3, từ ngày 15/09/2026 đến 30/12/2026,
                    lịch sẽ tự động xuất hiện vào tất cả các ngày Thứ 3
                    trong khoảng thời gian này.

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{ route('school-schedules.index') }}"
                       class="btn btn-light border">

                        Hủy

                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        Lưu lịch học

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
    const periodTimes = {
        1: { start: '07:30', end: '08:20' },
        2: { start: '08:20', end: '09:10' },
        3: { start: '09:25', end: '10:15' },
        4: { start: '10:15', end: '11:05' },
        5: { start: '11:05', end: '11:55' },
        6: { start: '13:00', end: '13:50' },
        7: { start: '13:50', end: '14:40' },
        8: { start: '14:55', end: '15:45' },
        9: { start: '15:45', end: '16:35' },
        10: { start: '16:35', end: '17:25' },
        11: { start: '17:30', end: '18:20' },
        12: { start: '18:20', end: '19:10' },
        13: { start: '19:25', end: '20:15' }
    };

    const startPeriodSelect =
        document.getElementById('start_period');

    const endPeriodSelect =
        document.getElementById('end_period');

    const preview =
        document.getElementById('period-time-preview');

    function updatePeriodPreview() {
        const startPeriod = startPeriodSelect.value;
        const endPeriod = endPeriodSelect.value;

        if (!startPeriod || !endPeriod) {
            preview.textContent = 'Chọn tiết để xem giờ';
            return;
        }

        if (Number(endPeriod) < Number(startPeriod)) {
            preview.textContent =
                'Tiết kết thúc phải lớn hơn hoặc bằng tiết bắt đầu.';

            preview.classList.add('text-danger');

            return;
        }

        preview.classList.remove('text-danger');

        preview.textContent =
            `${periodTimes[startPeriod].start} - ${periodTimes[endPeriod].end}`;
    }

    startPeriodSelect.addEventListener(
        'change',
        updatePeriodPreview
    );

    endPeriodSelect.addEventListener(
        'change',
        updatePeriodPreview
    );

    updatePeriodPreview();
</script>

@endpush