@extends('layouts.app')

@section('title', 'Thêm lịch học - AIStudy')

@section('content')

<div class="profile-page">

    <div class="profile-header">
        <div>
            <div class="profile-eyebrow">
                STUDY SCHEDULE
            </div>

            <h1>Thêm lịch học</h1>

            <p>
                Tạo kế hoạch học tập chi tiết để theo dõi tiến độ và hỗ trợ
                cá nhân hóa việc học.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="profile-alert">
            <strong>Vui lòng kiểm tra lại:</strong>

            <ul style="margin:10px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-card">

        <form method="POST" action="{{ route('study-schedules.store') }}">
            @csrf

            <h2>1. Thông tin buổi học</h2>

            <div class="form-group">
                <label for="title">Tên buổi học *</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Ví dụ: Học Laravel - Routing và Controller"
                    required
                >
            </div>

            <div class="form-group">
                <label for="subject">Môn học</label>

                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="Ví dụ: Lập trình Web, PHP, Laravel"
                >
            </div>

            <div class="form-group">
                <label for="learning_goal">Mục tiêu buổi học</label>

                <textarea
                    id="learning_goal"
                    name="learning_goal"
                    rows="3"
                    placeholder="Sau buổi học này bạn muốn đạt được điều gì?"
                >{{ old('learning_goal') }}</textarea>
            </div>

            <h2 style="margin-top:30px;">2. Nội dung học tập</h2>

            <div class="form-group">
                <label for="study_content">Nội dung cần học</label>

                <textarea
                    id="study_content"
                    name="study_content"
                    rows="5"
                    placeholder="Ví dụ:
- Tìm hiểu Route
- Tìm hiểu Controller
- Tạo route hiển thị dữ liệu
- Kết nối Controller với Blade"
                >{{ old('study_content') }}</textarea>
            </div>

            <div class="form-group">
                <label for="exercises">Bài tập cần thực hiện</label>

                <textarea
                    id="exercises"
                    name="exercises"
                    rows="4"
                    placeholder="Ví dụ:
- Tạo 3 route
- Tạo 1 Controller
- Hiển thị danh sách sản phẩm"
                >{{ old('exercises') }}</textarea>
            </div>

            <div class="form-group">
                <label for="materials">Tài liệu học tập</label>

                <textarea
                    id="materials"
                    name="materials"
                    rows="3"
                    placeholder="Nhập link tài liệu, tên file hoặc nguồn học tập..."
                >{{ old('materials') }}</textarea>
            </div>

            <div class="form-group">
                <label for="description">Ghi chú thêm</label>

                <textarea
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Các lưu ý khác cho buổi học..."
                >{{ old('description') }}</textarea>
            </div>

            <h2 style="margin-top:30px;">3. Thời gian học</h2>

            <div class="form-group">
                <label for="study_date">Ngày học *</label>

                <input
                    type="date"
                    id="study_date"
                    name="study_date"
                    value="{{ old('study_date', date('Y-m-d')) }}"
                    required
                >
            </div>

            <div style="
                display:grid;
                grid-template-columns:1fr 1fr;
                gap:20px;
            ">

                <div class="form-group">
                    <label for="start_time">Bắt đầu *</label>

                    <input
                        type="time"
                        id="start_time"
                        name="start_time"
                        value="{{ old('start_time') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="end_time">Kết thúc *</label>

                    <input
                        type="time"
                        id="end_time"
                        name="end_time"
                        value="{{ old('end_time') }}"
                        required
                    >
                </div>

            </div>

            <h2 style="margin-top:30px;">4. Mức độ ưu tiên</h2>

            <div class="form-group">
                <label for="priority">Ưu tiên</label>

                <select id="priority" name="priority" required>
                    <option value="low"
                        {{ old('priority') === 'low' ? 'selected' : '' }}>
                        Thấp
                    </option>

                    <option value="medium"
                        {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>
                        Trung bình
                    </option>

                    <option value="high"
                        {{ old('priority') === 'high' ? 'selected' : '' }}>
                        Cao
                    </option>
                </select>
            </div>

            <div style="
                display:flex;
                gap:12px;
                margin-top:25px;
                flex-wrap:wrap;
            ">

                <button
                    type="submit"
                    class="profile-save-btn"
                >
                    Lưu lịch học
                </button>

                <a
                    href="{{ route('study-schedules.index') }}"
                    class="profile-save-btn"
                    style="
                        text-decoration:none;
                        background:var(--surface-2);
                        color:var(--text);
                    "
                >
                    Quay lại
                </a>

            </div>

        </form>

    </div>

</div>

@endsection