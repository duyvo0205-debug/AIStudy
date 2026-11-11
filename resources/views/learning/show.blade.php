@extends('layouts.app')

@section('title', 'Chi tiết bài học')

@section('content')
<div class="container-fluid py-4">

    {{-- Tiêu đề --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                {{ $learning->title }}
            </h2>

            <p class="text-muted mb-0">
                Chi tiết nội dung học tập
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('learning.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Quay lại
            </a>

            <a href="{{ route('learning.edit', $learning->id) }}"
               class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>
                Chỉnh sửa
            </a>
        </div>
    </div>


    {{-- Thông tin tổng quan --}}
    <div class="row g-4 mb-4">

        {{-- Thông tin bài học --}}
        <div class="col-lg-8">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-book me-2"></i>
                        Nội dung bài học
                    </h5>
                </div>

                <div class="card-body">

                    {{-- Mô tả --}}
                    <div class="mb-4">
                        <h6 class="fw-bold">
                            Mô tả
                        </h6>

                        @if ($learning->description)
                            <p class="text-muted mb-0">
                                {{ $learning->description }}
                            </p>
                        @else
                            <p class="text-muted mb-0">
                                Chưa có mô tả cho bài học này.
                            </p>
                        @endif
                    </div>


                    {{-- Nội dung --}}
                    <div>
                        <h6 class="fw-bold mb-3">
                            Nội dung
                        </h6>

                        @if ($learning->content)
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($learning->content)) !!}
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-file-earmark-text fs-1"></i>

                                <p class="mt-2 mb-0">
                                    Chưa có nội dung chi tiết.
                                </p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>


        {{-- Thông tin bài học --}}
        <div class="col-lg-4">

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Thông tin
                    </h5>
                </div>

                <div class="card-body">

                    {{-- Môn học --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Môn học
                        </small>

                        <span class="fw-semibold">
                            {{ $learning->subject ?: 'Chưa phân loại' }}
                        </span>
                    </div>


                    {{-- Trình độ --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Trình độ
                        </small>

                        @if ($learning->difficulty === 'beginner')

                            <span class="badge bg-success">
                                Cơ bản
                            </span>

                        @elseif ($learning->difficulty === 'intermediate')

                            <span class="badge bg-warning text-dark">
                                Trung cấp
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Nâng cao
                            </span>

                        @endif
                    </div>


                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Trạng thái
                        </small>

                        @if ($learning->status === 'pending')

                            <span class="badge bg-secondary">
                                Chưa học
                            </span>

                        @elseif ($learning->status === 'in_progress')

                            <span class="badge bg-warning text-dark">
                                Đang học
                            </span>

                        @else

                            <span class="badge bg-success">
                                Đã hoàn thành
                            </span>

                        @endif
                    </div>


                    {{-- Ngày tạo --}}
                    <div>
                        <small class="text-muted d-block">
                            Ngày tạo
                        </small>

                        <span class="fw-semibold">
                            {{ $learning->created_at?->format('d/m/Y H:i') }}
                        </span>
                    </div>

                </div>
            </div>


            {{-- Cập nhật trạng thái --}}
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-arrow-repeat me-2"></i>
                        Tiến độ học tập
                    </h5>
                </div>

                <div class="card-body">

                    <form
                        action="{{ route('learning.status', $learning->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Trạng thái
                            </label>

                            <select
                                name="status"
                                class="form-select"
                            >
                                <option
                                    value="pending"
                                    {{ $learning->status === 'pending' ? 'selected' : '' }}
                                >
                                    Chưa học
                                </option>

                                <option
                                    value="in_progress"
                                    {{ $learning->status === 'in_progress' ? 'selected' : '' }}
                                >
                                    Đang học
                                </option>

                                <option
                                    value="completed"
                                    {{ $learning->status === 'completed' ? 'selected' : '' }}
                                >
                                    Đã hoàn thành
                                </option>
                            </select>
                        </div>

                        <button
                            type="submit"
                            class="btn btn-success w-100"
                        >
                            <i class="bi bi-check2-circle me-1"></i>
                            Cập nhật trạng thái
                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>


    {{-- Khu vực bài tập --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">

                <h5 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Bài tập liên quan
                </h5>

                <span class="badge bg-primary">
                    {{ $learning->exercises->count() }} bài
                </span>

            </div>
        </div>

        <div class="card-body">

            @if ($learning->exercises->count() > 0)

                <div class="list-group">

                    @foreach ($learning->exercises as $exercise)

                        <div class="list-group-item py-3">

                            <div class="d-flex justify-content-between align-items-start gap-3">

                                <div class="flex-grow-1">

                                    <h5 class="fw-bold mb-2">
                                        {{ $exercise->title }}
                                    </h5>

                                    <p class="text-muted mb-3">
                                        {{ $exercise->question }}
                                    </p>

                                    <div class="d-flex gap-2">

                                        @if ($exercise->difficulty === 'beginner')
                                            <span class="badge bg-success">
                                                Cơ bản
                                            </span>
                                        @elseif ($exercise->difficulty === 'intermediate')
                                            <span class="badge bg-warning text-dark">
                                                Trung cấp
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Nâng cao
                                            </span>
                                        @endif

                                    </div>

                                </div>

                                <div>
                                    <a
                                        href="{{ route('exercises.show', $exercise->id) }}"
                                        class="btn btn-primary"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>
                                        Làm bài
                                    </a>
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="text-center py-4 text-muted">

                    <i class="bi bi-journal-check fs-1"></i>

                    <p class="mt-2 mb-1 fw-semibold">
                        Chưa có bài tập
                    </p>

                    <small>
                        Chưa có bài tập nào được liên kết với nội dung học tập này.
                    </small>

                </div>

            @endif

        </div>
    </div>

</div>
@endsection