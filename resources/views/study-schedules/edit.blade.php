@extends('layouts.app')

@section('title', 'Chỉnh sửa lịch học - AIStudy')

@section('content')

<div class="profile-page">

    <div class="profile-header">
        <div>
            <div class="profile-eyebrow">
                STUDY SCHEDULE
            </div>

            <h1>Chỉnh sửa lịch học</h1>

            <p>
                Cập nhật kế hoạch, thời gian và kết quả học tập.
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

        <form
            method="POST"
            action="{{ route('study-schedules.update', $schedule) }}"
        >

            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Tên buổi học *</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $schedule->title) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="subject">Môn học</label>

                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="{{ old('subject', $schedule->subject) }}"
                >
            </div>

            <div class="form-group">
                <label for="learning_goal">Mục tiêu buổi học</label>

                <textarea
                    id="learning_goal"
                    name="learning_goal"
                    rows="3"
                >{{ old('learning_goal', $schedule->learning_goal) }}</textarea>
            </div>

            <div class="form-group">
                <label for="study_content">Nội dung cần học</label>

                <textarea
                    id="study_content"
                    name="study_content"
                    rows="5"
                >{{ old('study_content', $schedule->study_content) }}</textarea>
            </div>

            <div class="form-group">
                <label for="exercises">Bài tập cần thực hiện</label>

                <textarea
                    id="exercises"
                    name="exercises"
                    rows="4"
                >{{ old('exercises', $schedule->exercises) }}</textarea>
            </div>

            <div class="form-group">
                <label for="materials">Tài liệu học tập</label>

                <textarea
                    id="materials"
                    name="materials"
                    rows="3"
                >{{ old('materials', $schedule->materials) }}</textarea>
            </div>

            <div class="form-group">
                <label for="description">Ghi chú thêm</label>

                <textarea
                    id="description"
                    name="description"
                    rows="3"
                >{{ old('description', $schedule->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="result_note">Kết quả sau khi học</label>

                <textarea
                    id="result_note"
                    name="result_note"
                    rows="4"
                    placeholder="Bạn đã học được gì? Còn phần nào chưa hoàn thành?"
                >{{ old('result_note', $schedule->result_note) }}</textarea>
            </div>

            <div class="form-group">
                <label for="study_date">Ngày học *</label>

                <input
                    type="date"
                    id="study_date"
                    name="study_date"
                    value="{{ old('study_date', $schedule->study_date->format('Y-m-d')) }}"
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
                        value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="end_time">Kết thúc *</label>

                    <input
                        type="time"
                        id="end_time"
                        name="end_time"
                        value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}"
                        required
                    >
                </div>

            </div>

            <div class="form-group">
                <label for="priority">Mức độ ưu tiên</label>

                <select id="priority" name="priority" required>
                    <option value="low"
                        {{ old('priority', $schedule->priority) === 'low' ? 'selected' : '' }}>
                        Thấp
                    </option>

                    <option value="medium"
                        {{ old('priority', $schedule->priority) === 'medium' ? 'selected' : '' }}>
                        Trung bình
                    </option>

                    <option value="high"
                        {{ old('priority', $schedule->priority) === 'high' ? 'selected' : '' }}>
                        Cao
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>

                <select id="status" name="status" required>
                    <option value="planned"
                        {{ old('status', $schedule->status) === 'planned' ? 'selected' : '' }}>
                        Đã lên kế hoạch
                    </option>

                    <option value="completed"
                        {{ old('status', $schedule->status) === 'completed' ? 'selected' : '' }}>
                        Đã hoàn thành
                    </option>

                    <option value="cancelled"
                        {{ old('status', $schedule->status) === 'cancelled' ? 'selected' : '' }}>
                        Đã hủy
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
                    Cập nhật lịch học
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