@extends('layouts.app')

@section('title', 'Mục tiêu học tập - AIStudy')

@section('content')

<div class="profile-page">

    <div class="profile-header">

        <div>
            <div class="profile-eyebrow">
                LEARNING GOALS
            </div>

            <h1>Mục tiêu học tập</h1>

            <p>
                Thiết lập và theo dõi những mục tiêu bạn muốn đạt được.
                AIStudy sẽ sử dụng các mục tiêu này để cá nhân hóa lộ trình học.
            </p>
        </div>

        <div>
            <a
                href="{{ route('learning-goals.create') }}"
                class="profile-save-btn"
                style="text-decoration:none;"
            >
                + Thêm mục tiêu
            </a>
        </div>

    </div>


    @if(session('success'))
        <div class="profile-alert success">
            ✓ {{ session('success') }}
        </div>
    @endif


    @if($goals->count() === 0)

        <div class="profile-card">

            <div style="text-align:center; padding:50px 20px;">

                <div style="font-size:50px; margin-bottom:15px;">
                    🎯
                </div>

                <h2>
                    Chưa có mục tiêu học tập
                </h2>

                <p style="color:var(--muted);">
                    Hãy tạo mục tiêu đầu tiên để AIStudy bắt đầu
                    cá nhân hóa quá trình học của bạn.
                </p>

                <a
                    href="{{ route('learning-goals.create') }}"
                    class="profile-save-btn"
                    style="text-decoration:none; margin-top:15px;"
                >
                    + Tạo mục tiêu đầu tiên
                </a>

            </div>

        </div>

    @else

        <div class="profile-grid">

            @foreach($goals as $goal)

                <div class="profile-card">

                    <div class="card-title">

                        <div class="card-icon">
                            🎯
                        </div>

                        <div>
                            <h2>
                                {{ $goal->title }}
                            </h2>

                            <p>
                                {{ $goal->subject ?: 'Mục tiêu cá nhân' }}
                            </p>
                        </div>

                    </div>


                    @if($goal->description)

                        <p style="color:var(--muted); font-size:13px;">
                            {{ $goal->description }}
                        </p>

                    @endif


                    <div style="margin-top:20px;">

                        <div class="form-group">

                            <label>
                                Trình độ hiện tại
                            </label>

                            <div>
                                {{ ucfirst($goal->current_level) }}
                            </div>

                        </div>


                        <div class="form-group">

                            <label>
                                Mục tiêu
                            </label>

                            <div>
                                {{ ucfirst($goal->target_level) }}
                            </div>

                        </div>


                        <div class="form-group">

                            <label>
                                Thời gian học mỗi ngày
                            </label>

                            <div>
                                {{ $goal->daily_minutes }} phút
                            </div>

                        </div>


                        @if($goal->target_date)

                            <div class="form-group">

                                <label>
                                    Thời hạn
                                </label>

                                <div>
                                    {{ $goal->target_date->format('d/m/Y') }}
                                </div>

                            </div>

                        @endif


                        <div class="form-group">

                            <label>
                                Trạng thái
                            </label>

                            <form
                                method="POST"
                                action="{{ route('learning-goals.status', ['goal' => $goal->id]) }}"
                            >
                                @csrf
                                @method('PUT')

                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                >
                                    <option
                                        value="active"
                                        {{ $goal->status === 'active' ? 'selected' : '' }}
                                    >
                                        Đang học
                                    </option>

                                    <option
                                        value="completed"
                                        {{ $goal->status === 'completed' ? 'selected' : '' }}
                                    >
                                        Đã hoàn thành
                                    </option>

                                    <option
                                        value="paused"
                                        {{ $goal->status === 'paused' ? 'selected' : '' }}
                                    >
                                        Tạm dừng
                                    </option>
                                </select>

                            </form>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('learning-goals.destroy', ['goal' => $goal->id]) }}"~
                        onsubmit="return confirm('Bạn có chắc muốn xóa mục tiêu này?');"
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
                            Xóa mục tiêu
                        </button>

                    </form>

                </div>

            @endforeach

        </div>

    @endif

</div>

@endsection