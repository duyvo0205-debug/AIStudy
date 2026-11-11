@extends('layouts.app')

@section('title', 'Hồ sơ - AIStudy')

@section('content')

<div class="profile-page">

    {{-- Header --}}
    <div class="profile-header">
        <div>
            <div class="profile-eyebrow">TÀI KHOẢN</div>
            <h1>Hồ sơ cá nhân</h1>
            <p>
                Quản lý thông tin cá nhân và thiết lập thông tin học tập
                để AIStudy cá nhân hóa lộ trình cho bạn.
            </p>
        </div>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="profile-alert success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="profile-alert error">
            <strong>Vui lòng kiểm tra lại:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="profile-grid">

            {{-- Thông tin tài khoản --}}
            <div class="profile-card">

                <div class="card-title">
                    <div class="card-icon">👤</div>

                    <div>
                        <h2>Thông tin tài khoản</h2>
                        <p>Thông tin cơ bản của bạn</p>
                    </div>
                </div>

                <div class="profile-user">

                    <div class="profile-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div>
                        <h3>{{ $user->name }}</h3>
                        <span>{{ $user->email }}</span>
                    </div>

                </div>

                <div class="form-group">
                    <label>Họ và tên</label>

                    <input
                        type="text"
                        value="{{ $user->name }}"
                        disabled
                    >

                    <small>
                        Họ tên được quản lý từ tài khoản đăng nhập.
                    </small>
                </div>

                <div class="form-group">
                    <label>Email</label>

                    <input
                        type="email"
                        value="{{ $user->email }}"
                        disabled
                    >
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $profile->phone) }}"
                        placeholder="Nhập số điện thoại"
                    >
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Ngày sinh</label>

                    <input
                        type="date"
                        id="date_of_birth"
                        name="date_of_birth"
                        value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}"
                    >
                </div>

            </div>


            {{-- Thông tin học tập --}}
            <div class="profile-card">

                <div class="card-title">
                    <div class="card-icon">🎓</div>

                    <div>
                        <h2>Thông tin học tập</h2>
                        <p>Dùng để AI cá nhân hóa việc học</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="education_level">
                        Trình độ học vấn
                    </label>

                    <select
                        id="education_level"
                        name="education_level"
                    >
                        <option value="">
                            Chọn trình độ
                        </option>

                        <option
                            value="THPT"
                            {{ old('education_level', $profile->education_level) == 'THPT' ? 'selected' : '' }}
                        >
                            THPT
                        </option>

                        <option
                            value="Cao đẳng"
                            {{ old('education_level', $profile->education_level) == 'Cao đẳng' ? 'selected' : '' }}
                        >
                            Cao đẳng
                        </option>

                        <option
                            value="Đại học"
                            {{ old('education_level', $profile->education_level) == 'Đại học' ? 'selected' : '' }}
                        >
                            Đại học
                        </option>

                        <option
                            value="Sau đại học"
                            {{ old('education_level', $profile->education_level) == 'Sau đại học' ? 'selected' : '' }}
                        >
                            Sau đại học
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="major">Chuyên ngành</label>

                    <input
                        type="text"
                        id="major"
                        name="major"
                        value="{{ old('major', $profile->major) }}"
                        placeholder="Ví dụ: Công nghệ thông tin"
                    >
                </div>

                <div class="form-group">
                    <label for="learning_level">
                        Trình độ học tập hiện tại
                    </label>

                    <select
                        id="learning_level"
                        name="learning_level"
                    >
                        <option
                            value="beginner"
                            {{ old('learning_level', $profile->learning_level) == 'beginner' ? 'selected' : '' }}
                        >
                            Cơ bản
                        </option>

                        <option
                            value="intermediate"
                            {{ old('learning_level', $profile->learning_level) == 'intermediate' ? 'selected' : '' }}
                        >
                            Trung cấp
                        </option>

                        <option
                            value="advanced"
                            {{ old('learning_level', $profile->learning_level) == 'advanced' ? 'selected' : '' }}
                        >
                            Nâng cao
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="daily_study_target">
                        Mục tiêu học mỗi ngày
                    </label>

                    <div class="input-with-unit">
                        <input
                            type="number"
                            id="daily_study_target"
                            name="daily_study_target"
                            min="15"
                            max="1440"
                            value="{{ old('daily_study_target', $profile->daily_study_target) }}"
                        >

                        <span>phút</span>
                    </div>

                    <small>
                        AI sẽ sử dụng mục tiêu này để đề xuất lịch học.
                    </small>
                </div>

            </div>


            {{-- Thời gian học --}}
            <div class="profile-card profile-card-full">

                <div class="card-title">
                    <div class="card-icon">🕐</div>

                    <div>
                        <h2>Thời gian học ưu tiên</h2>
                        <p>
                            Cho AI biết khoảng thời gian bạn thường học hiệu quả nhất.
                        </p>
                    </div>
                </div>

                <div class="time-grid">

                    <div class="form-group">
                        <label for="preferred_study_start">
                            Bắt đầu học
                        </label>

                        <input
                            type="time"
                            id="preferred_study_start"
                            name="preferred_study_start"
                            value="{{ old('preferred_study_start', $profile->preferred_study_start) }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="preferred_study_end">
                            Kết thúc học
                        </label>

                        <input
                            type="time"
                            id="preferred_study_end"
                            name="preferred_study_end"
                            value="{{ old('preferred_study_end', $profile->preferred_study_end) }}"
                        >
                    </div>

                </div>

            </div>

        </div>


        {{-- Button --}}
        <div class="profile-actions">

            <button
                type="submit"
                class="profile-save-btn"
            >
                <span>✓</span>
                Lưu thay đổi
            </button>

        </div>

    </form>

</div>

@endsection