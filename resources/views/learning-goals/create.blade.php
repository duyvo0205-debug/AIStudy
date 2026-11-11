@extends('layouts.app')

@section('title', 'Tạo mục tiêu - AIStudy')

@section('content')

<div class="profile-page">

    <div class="profile-header">

        <div class="profile-eyebrow">
            NEW GOAL
        </div>

        <h1>Tạo mục tiêu học tập</h1>

        <p>
            Cho AIStudy biết bạn muốn đạt được điều gì.
        </p>

    </div>

    @if($errors->any())

        <div class="profile-alert error">

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form
        method="POST"
        action="{{ route('learning-goals.store') }}"
    >

        @csrf

        <div class="profile-card">

            <div class="form-group">

                <label for="title">
                    Tên mục tiêu *
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Ví dụ: Thành thạo SQL"
                    required
                >

            </div>

            <div class="form-group">

                <label for="subject">
                    Môn học / chủ đề
                </label>

                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="Ví dụ: SQL, PHP, Laravel..."
                >

            </div>

            <div class="form-group">

                <label for="description">
                    Mô tả
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    style="
                        width: 100%;
                        padding: 14px;
                        border: 1px solid var(--border);
                        border-radius: 10px;
                        font-family: inherit;
                        resize: vertical;
                        box-sizing: border-box;
                    "
                    placeholder="Bạn muốn đạt được điều gì?"
                >{{ old('description') }}</textarea>

            </div>

            <div class="time-grid">

                <div class="form-group">

                    <label for="current_level">
                        Trình độ hiện tại
                    </label>

                    <select
                        id="current_level"
                        name="current_level"
                        required
                    >

                        <option
                            value="beginner"
                            {{ old('current_level', 'beginner') === 'beginner' ? 'selected' : '' }}
                        >
                            Cơ bản
                        </option>

                        <option
                            value="intermediate"
                            {{ old('current_level') === 'intermediate' ? 'selected' : '' }}
                        >
                            Trung cấp
                        </option>

                        <option
                            value="advanced"
                            {{ old('current_level') === 'advanced' ? 'selected' : '' }}
                        >
                            Nâng cao
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="target_level">
                        Trình độ muốn đạt
                    </label>

                    <select
                        id="target_level"
                        name="target_level"
                        required
                    >

                        <option
                            value="beginner"
                            {{ old('target_level') === 'beginner' ? 'selected' : '' }}
                        >
                            Cơ bản
                        </option>

                        <option
                            value="intermediate"
                            {{ old('target_level', 'intermediate') === 'intermediate' ? 'selected' : '' }}
                        >
                            Trung cấp
                        </option>

                        <option
                            value="advanced"
                            {{ old('target_level') === 'advanced' ? 'selected' : '' }}
                        >
                            Nâng cao
                        </option>

                    </select>

                </div>

            </div>

            <div class="time-grid">

                <div class="form-group">

                    <label for="target_date">
                        Thời hạn
                    </label>

                    <input
                        type="date"
                        id="target_date"
                        name="target_date"
                        value="{{ old('target_date') }}"
                    >

                </div>

                <div class="form-group">

                    <label for="daily_minutes">
                        Thời gian học mỗi ngày
                    </label>

                    <input
                        type="number"
                        id="daily_minutes"
                        name="daily_minutes"
                        min="15"
                        max="1440"
                        value="{{ old('daily_minutes', 60) }}"
                        required
                    >

                </div>

            </div>

            <div class="profile-actions">

                <a
                    href="{{ route('learning-goals.index') }}"
                    style="
                        padding: 13px 20px;
                        color: var(--muted);
                        text-decoration: none;
                        font-size: 13px;
                        font-weight: 600;
                    "
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="profile-save-btn"
                >
                    ✓ Tạo mục tiêu
                </button>

            </div>

        </div>

    </form>

</div>

@endsection