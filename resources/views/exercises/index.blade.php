@extends('layouts.app')

@section('title', 'Bài tập')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Bài tập
            </h2>

            <p class="text-muted mb-0">
                Luyện tập và kiểm tra kiến thức của bạn
            </p>
        </div>

        <a
            href="{{ route('exercises.create') }}"
            class="btn btn-primary"
        >
            + Tạo bài tập
        </a>

    </div>


    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    @if ($exercises->count() > 0)

        <div class="row g-4">

            @foreach ($exercises as $exercise)

                <div class="col-md-6 col-xl-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-3">

                                <span class="badge bg-primary-subtle text-primary">
                                    @if ($exercise->difficulty === 'beginner')
                                        Cơ bản
                                    @elseif ($exercise->difficulty === 'intermediate')
                                        Trung bình
                                    @else
                                        Nâng cao
                                    @endif
                                </span>

                                @if ($exercise->status === 'active')
                                    <span class="badge bg-success-subtle text-success">
                                        Đang hoạt động
                                    </span>
                                @endif

                            </div>


                            <h5 class="fw-bold">
                                {{ $exercise->title }}
                            </h5>


                            @if ($exercise->learningContent)

                                <p class="text-muted small mb-2">
                                    📚
                                    {{ $exercise->learningContent->title }}
                                </p>

                            @endif


                            <p class="text-muted">
                                {{ Str::limit($exercise->question, 120) }}
                            </p>


                            <div class="d-flex gap-2 mt-3">

                                <a
                                    href="{{ route('exercises.show', $exercise) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    Làm bài
                                </a>

                                <a
                                    href="{{ route('exercises.edit', $exercise) }}"
                                    class="btn btn-outline-secondary btn-sm"
                                >
                                    Sửa
                                </a>


                                <form
                                    action="{{ route('exercises.destroy', $exercise) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa bài tập này?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <div style="font-size: 50px;">
                    📝
                </div>

                <h4 class="fw-bold mt-3">
                    Chưa có bài tập
                </h4>

                <p class="text-muted">
                    Hãy tạo bài tập đầu tiên để bắt đầu luyện tập.
                </p>

                <a
                    href="{{ route('exercises.create') }}"
                    class="btn btn-primary"
                >
                    + Tạo bài tập
                </a>

            </div>

        </div>

    @endif

</div>

@endsection