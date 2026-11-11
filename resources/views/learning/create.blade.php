@extends('layouts.app')

@section('title', 'Thêm bài học')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Thêm bài học</h2>

            <p class="text-muted mb-0">
                Tạo nội dung học tập mới
            </p>
        </div>

        <a href="{{ route('learning.index') }}"
           class="btn btn-outline-secondary">
            Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Vui lòng kiểm tra lại:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                Thông tin bài học
            </h5>
        </div>

        <div class="card-body">

            <form action="{{ route('learning.store') }}"
                  method="POST">

                @csrf

                <div class="row">

                    {{-- Tên bài học --}}
                    <div class="col-md-8 mb-3">

                        <label for="title"
                               class="form-label fw-semibold">
                            Tên bài học
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="title"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}"
                            placeholder="Ví dụ: Học vòng lặp trong PHP"
                            required
                        >

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Trình độ --}}
                    <div class="col-md-4 mb-3">

                        <label for="difficulty"
                               class="form-label fw-semibold">
                            Trình độ
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            id="difficulty"
                            name="difficulty"
                            class="form-select @error('difficulty') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                -- Chọn trình độ --
                            </option>

                            <option value="beginner"
                                {{ old('difficulty') == 'beginner' ? 'selected' : '' }}>
                                Cơ bản
                            </option>

                            <option value="intermediate"
                                {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>
                                Trung bình
                            </option>

                            <option value="advanced"
                                {{ old('difficulty') == 'advanced' ? 'selected' : '' }}>
                                Nâng cao
                            </option>
                        </select>

                        @error('difficulty')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>
                    {{-- Môn học --}}
                    <div class="col-md-6 mb-3">
                        <label for="subject"
                            class="form-label fw-semibold">
                            Môn học
                        </label>

                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            class="form-control @error('subject') is-invalid @enderror"
                            value="{{ old('subject') }}"
                            placeholder="Ví dụ: PHP, Laravel, Cơ sở dữ liệu..."
                        >

                        @error('subject')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Mô tả --}}
                    <div class="col-md-6 mb-3">
                        <label for="description"
                            class="form-label fw-semibold">
                            Mô tả bài học
                        </label>

                        <input
                            type="text"
                            id="description"
                            name="description"
                            class="form-control @error('description') is-invalid @enderror"
                            value="{{ old('description') }}"
                            placeholder="Ví dụ: Tìm hiểu cách truy vấn dữ liệu bằng Eloquent..."
                        >

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Nội dung bài học --}}
                    <div class="col-md-12 mb-3">

                        <label for="content"
                               class="form-label fw-semibold">
                            Nội dung bài học
                        </label>

                        <textarea
                            id="content"
                            name="content"
                            rows="6"
                            class="form-control @error('content') is-invalid @enderror"
                            placeholder="Nhập nội dung cần học..."
                        >{{ old('content') }}</textarea>

                        @error('content')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Trạng thái --}}
                    <div class="col-md-6 mb-3">

                        <label for="status"
                               class="form-label fw-semibold">
                            Trạng thái
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="pending"
                                {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                Chưa học
                            </option>

                            <option value="in_progress"
                                {{ old('status') == 'in_progress' ? 'selected' : '' }}>
                                Đang học
                            </option>

                            <option value="completed"
                                {{ old('status') == 'completed' ? 'selected' : '' }}>
                                Đã hoàn thành
                            </option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="d-flex gap-2 mt-3">

                    <button type="submit"
                            class="btn btn-primary">
                        Lưu bài học
                    </button>

                    <a href="{{ route('learning.index') }}"
                       class="btn btn-light border">
                        Hủy
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection