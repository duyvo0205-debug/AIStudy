@extends('layouts.app')

@section('title', 'Lịch học - AIStudy')

@section('content')

<div class="profile-page">

    {{-- HEADER --}}
    <div class="profile-header">

        <div>
            <div class="profile-eyebrow">
                STUDY SCHEDULE
            </div>

            <h1>Quản lý thời gian học</h1>

            <p>
                Lập kế hoạch, theo dõi nội dung và kết quả học tập.
                AIStudy sẽ sử dụng dữ liệu này để hỗ trợ cá nhân hóa việc học.
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


    {{-- THÔNG BÁO THÀNH CÔNG --}}
    @if(session('success'))

        <div class="profile-alert success">
            ✓ {{ session('success') }}
        </div>

    @endif


    {{-- THÔNG BÁO LỖI --}}
    @if(session('error'))

        <div class="profile-alert">
            ✕ {{ session('error') }}
        </div>

    @endif


    {{-- KHÔNG CÓ LỊCH HỌC --}}
    @if($schedules->count() === 0)

        <div class="profile-card">

            <div style="text-align:center; padding:50px 20px;">

                <div style="font-size:50px; margin-bottom:15px;">
                    ⏰
                </div>

                <h2>Chưa có lịch học</h2>

                <p style="color:var(--muted);">
                    Hãy tạo lịch học đầu tiên để bắt đầu quản lý
                    thời gian và kế hoạch học tập của bạn.
                </p>

                <a
                    href="{{ route('study-schedules.create') }}"
                    class="profile-save-btn"
                    style="
                        display:inline-block;
                        text-decoration:none;
                        margin-top:15px;
                    "
                >
                    + Tạo lịch học đầu tiên
                </a>

            </div>

        </div>


    {{-- CÓ LỊCH HỌC --}}
    @else

        <div class="profile-grid">

            @foreach($schedules as $schedule)

                <div class="profile-card">

                    {{-- TIÊU ĐỀ --}}
                    <div class="card-title">

                        <div class="card-icon">
                            ⏰
                        </div>

                        <div style="flex:1;">

                            <h2>
                                {{ $schedule->title }}
                            </h2>

                            <p>
                                {{ $schedule->study_date->format('d/m/Y') }}
                            </p>

                        </div>

                    </div>


                    {{-- MÔN HỌC --}}
                    @if($schedule->subject)

                        <div class="form-group">

                            <label>
                                Môn học
                            </label>

                            <div>
                                {{ $schedule->subject }}
                            </div>

                        </div>

                    @endif


                    {{-- MỤC TIÊU --}}
                    @if($schedule->learning_goal)

                        <div class="form-group">

                            <label>
                                Mục tiêu buổi học
                            </label>

                            <div style="white-space:pre-line;">
                                {{ $schedule->learning_goal }}
                            </div>

                        </div>

                    @endif


                    {{-- NỘI DUNG HỌC --}}
                    @if($schedule->study_content)

                        <div class="form-group">

                            <label>
                                Nội dung cần học
                            </label>

                            <div style="
                                white-space:pre-line;
                                line-height:1.7;
                            ">
                                {{ $schedule->study_content }}
                            </div>

                        </div>

                    @endif


                    {{-- BÀI TẬP --}}
                    @if($schedule->exercises)

                        <div class="form-group">

                            <label>
                                Bài tập cần thực hiện
                            </label>

                            <div style="
                                white-space:pre-line;
                                line-height:1.7;
                            ">
                                {{ $schedule->exercises }}
                            </div>

                        </div>

                    @endif


                    {{-- TÀI LIỆU --}}
                    @if($schedule->materials)

                        <div class="form-group">

                            <label>
                                Tài liệu học tập
                            </label>

                            <div style="
                                white-space:pre-line;
                                line-height:1.7;
                            ">
                                {{ $schedule->materials }}
                            </div>

                        </div>

                    @endif


                    {{-- MÔ TẢ --}}
                    @if($schedule->description)

                        <div class="form-group">

                            <label>
                                Ghi chú thêm
                            </label>

                            <div style="
                                white-space:pre-line;
                                line-height:1.7;
                                color:var(--muted);
                            ">
                                {{ $schedule->description }}
                            </div>

                        </div>

                    @endif


                    {{-- THÔNG TIN THỜI GIAN --}}
                    <div
                        style="
                            margin-top:20px;
                            padding-top:15px;
                            border-top:1px solid var(--border);
                        "
                    >

                        <div class="form-group">

                            <label>
                                Thời gian học
                            </label>

                            <div>
                                {{ substr($schedule->start_time, 0, 5) }}
                                -
                                {{ substr($schedule->end_time, 0, 5) }}
                            </div>

                        </div>


                        <div class="form-group">

                            <label>
                                Thời lượng
                            </label>

                            <div>
                                {{ $schedule->duration_minutes }} phút
                            </div>

                        </div>


                        {{-- MỨC ĐỘ ƯU TIÊN --}}
                        <div class="form-group">

                            <label>
                                Mức độ ưu tiên
                            </label>

                            <div>

                                @if($schedule->priority === 'high')

                                    <span
                                        style="
                                            color:#e5484d;
                                            font-weight:600;
                                        "
                                    >
                                        🔴 Cao
                                    </span>

                                @elseif($schedule->priority === 'medium')

                                    <span
                                        style="
                                            color:#d99a00;
                                            font-weight:600;
                                        "
                                    >
                                        🟡 Trung bình
                                    </span>

                                @else

                                    <span
                                        style="
                                            color:#2e9b63;
                                            font-weight:600;
                                        "
                                    >
                                        🟢 Thấp
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- TRẠNG THÁI --}}
                        <div class="form-group">

                            <label>
                                Trạng thái
                            </label>

                            <form
                                method="POST"
                                action="{{ route('study-schedules.status', $schedule) }}"
                            >

                                @csrf
                                @method('PUT')

                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                >

                                    <option
                                        value="planned"
                                        {{ $schedule->status === 'planned' ? 'selected' : '' }}
                                    >
                                        Đã lên kế hoạch
                                    </option>

                                    <option
                                        value="completed"
                                        {{ $schedule->status === 'completed' ? 'selected' : '' }}
                                    >
                                        Đã hoàn thành
                                    </option>

                                    <option
                                        value="cancelled"
                                        {{ $schedule->status === 'cancelled' ? 'selected' : '' }}
                                    >
                                        Đã hủy
                                    </option>

                                </select>

                            </form>

                        </div>

                    </div>


                    {{-- KẾT QUẢ SAU KHI HỌC --}}
                    @if($schedule->result_note)

                        <div
                            style="
                                margin-top:20px;
                                padding:15px;
                                border-radius:12px;
                                background:var(--surface-2);
                            "
                        >

                            <label>
                                Kết quả sau khi học
                            </label>

                            <div style="
                                margin-top:8px;
                                white-space:pre-line;
                                line-height:1.7;
                            ">
                                {{ $schedule->result_note }}
                            </div>

                        </div>

                    @endif


                    {{-- NÚT CHỨC NĂNG --}}
                    <div
                        style="
                            display:flex;
                            gap:15px;
                            align-items:center;
                            flex-wrap:wrap;
                            margin-top:25px;
                            padding-top:15px;
                            border-top:1px solid var(--border);
                        "
                    >

                        {{-- CHỈNH SỬA --}}
                        <a
                            href="{{ route('study-schedules.edit', $schedule) }}"
                            class="profile-save-btn"
                            style="
                                display:inline-block;
                                text-decoration:none;
                            "
                        >
                            Chỉnh sửa
                        </a>


                        {{-- XÓA --}}
                        <form
                            method="POST"
                            action="{{ route('study-schedules.destroy', $schedule) }}"
                            onsubmit="return confirm('Bạn có chắc muốn xóa lịch học này?');"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                style="
                                    border:none;
                                    background:none;
                                    color:#e5484d;
                                    cursor:pointer;
                                    padding:0;
                                    font-size:13px;
                                "
                            >
                                Xóa lịch học
                            </button>

                        </form>

                    </div>

                </div>

            @endforeach

        </div>

    @endif

</div>

@endsection