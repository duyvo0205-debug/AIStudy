@extends('layouts.app')

@section('title', 'Học tập')

@section('content')
<div class="container-fluid py-4">

    {{-- Tiêu đề --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Học tập</h2>
            <p class="text-muted mb-0">
                Quản lý nội dung và tiến độ học tập của bạn
            </p>
        </div>

        <a href="{{ route('learning.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Thêm bài học
        </a>
    </div>

    {{-- Thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- Thống kê --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Tổng bài học
                    </div>

                    <h3 class="fw-bold mb-0">
                        {{ $contents->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Đang học
                    </div>

                    <h3 class="fw-bold text-warning mb-0">
                        {{ $contents->where('status', 'in_progress')->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">
                        Đã hoàn thành
                    </div>

                    <h3 class="fw-bold text-success mb-0">
                        {{ $contents->where('status', 'completed')->count() }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Danh sách bài học --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Danh sách bài học
            </h5>
        </div>

        <div class="card-body">

            @if ($contents->isEmpty())

                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-book fs-1 text-muted"></i>
                    </div>

                    <h5 class="fw-semibold">
                        Chưa có bài học nào
                    </h5>

                    <p class="text-muted">
                        Hãy tạo bài học đầu tiên của bạn.
                    </p>

                    <a href="{{ route('learning.create') }}"
                       class="btn btn-primary">
                        Thêm bài học
                    </a>
                </div>

            @else

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên bài học</th>
                                <th>Môn học</th>
                                <th>Ngày học</th>
                                <th>Thời lượng</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($contents as $content)
                                <tr>
                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $content->title }}
                                        </div>

                                        @if ($content->description)
                                            <small class="text-muted">
                                                {{ Str::limit($content->description, 70) }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $content->subject ?: 'Chưa phân loại' }}
                                    </td>

                                    <td>
                                        {{ $content->study_date
                                            ? $content->study_date->format('d/m/Y')
                                            : 'Chưa đặt' }}
                                    </td>

                                    <td>
                                        {{ $content->duration_minutes }} phút
                                    </td>

                                    <td>
                                        @if ($content->status === 'pending')
                                            <span class="badge bg-secondary">
                                                Chưa học
                                            </span>
                                        @elseif ($content->status === 'in_progress')
                                            <span class="badge bg-warning text-dark">
                                                Đang học
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                Đã hoàn thành
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-end">
    <div class="d-flex justify-content-end gap-2">

        {{-- Bắt đầu học --}}
        @if ($content->status === 'pending')

            <form
                action="{{ route('learning.start', $content->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <button
                    type="submit"
                    class="btn btn-sm btn-outline-success"
                    title="Bắt đầu học"
                >
                    <i class="bi bi-play-fill me-1"></i>
                    Bắt đầu
                </button>
            </form>

        @elseif ($content->status === 'in_progress')

            {{-- Xem nội dung đang học --}}
            <a
                href="{{ route('learning.show', $content->id) }}"
                class="btn btn-sm btn-primary"
                title="Tiếp tục học"
            >
                <i class="bi bi-book me-1"></i>
                Tiếp tục
            </a>

        @endif


        {{-- Xem chi tiết --}}
        <a
            href="{{ route('learning.show', $content->id) }}"
            class="btn btn-sm btn-outline-secondary"
            title="Xem chi tiết"
        >
            <i class="bi bi-eye"></i>
        </a>


        {{-- Sửa --}}
        <a
            href="{{ route('learning.edit', $content->id) }}"
            class="btn btn-sm btn-outline-primary"
            title="Sửa"
        >
            <i class="bi bi-pencil"></i>
        </a>


        {{-- Xóa --}}
        <form
            action="{{ route('learning.destroy', $content->id) }}"
            method="POST"
            onsubmit="return confirm('Bạn có chắc muốn xóa bài học này không?')"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="btn btn-sm btn-outline-danger"
                title="Xóa"
            >
                <i class="bi bi-trash"></i>
            </button>
        </form>

    </div>
</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @endif

        </div>
    </div>

</div>
@endsection