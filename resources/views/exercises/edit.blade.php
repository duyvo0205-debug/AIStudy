@extends('layouts.app')

@section('title', 'Chỉnh sửa bài tập - AIStudy')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Chỉnh sửa bài tập</h2>
        <p class="text-muted mb-0">
            Cập nhật thông tin và đáp án của bài tập.
        </p>
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

    <form action="{{ route('exercises.update', $exercise) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                {{-- Nội dung học --}}
                <div class="mb-3">
                    <label for="learning_content_id" class="form-label fw-semibold">
                        Nội dung học
                    </label>

                    <select
                        name="learning_content_id"
                        id="learning_content_id"
                        class="form-select"
                        required
                    >
                        @foreach ($contents as $content)
                            <option
                                value="{{ $content->id }}"
                                @selected(
                                    (int) old(
                                        'learning_content_id',
                                        $exercise->learning_content_id
                                    ) === (int) $content->id
                                )
                            >
                                {{ $content->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tiêu đề --}}
                <div class="mb-3">
                    <label for="title" class="form-label fw-semibold">
                        Tiêu đề bài tập
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="form-control"
                        value="{{ old('title', $exercise->title) }}"
                        required
                    >
                </div>

                {{-- Câu hỏi --}}
                <div class="mb-3">
                    <label for="question" class="form-label fw-semibold">
                        Câu hỏi
                    </label>

                    <textarea
                        name="question"
                        id="question"
                        class="form-control"
                        rows="4"
                        required
                    >{{ old('question', $exercise->question) }}</textarea>
                </div>

                {{-- Đáp án A --}}
                <div class="mb-3">
                    <label for="option_a" class="form-label fw-semibold">
                        Đáp án A
                    </label>

                    <input
                        type="text"
                        name="option_a"
                        id="option_a"
                        class="form-control"
                        value="{{ old('option_a', $exercise->option_a) }}"
                        required
                    >
                </div>

                {{-- Đáp án B --}}
                <div class="mb-3">
                    <label for="option_b" class="form-label fw-semibold">
                        Đáp án B
                    </label>

                    <input
                        type="text"
                        name="option_b"
                        id="option_b"
                        class="form-control"
                        value="{{ old('option_b', $exercise->option_b) }}"
                        required
                    >
                </div>

                {{-- Đáp án C --}}
                <div class="mb-3">
                    <label for="option_c" class="form-label fw-semibold">
                        Đáp án C
                    </label>

                    <input
                        type="text"
                        name="option_c"
                        id="option_c"
                        class="form-control"
                        value="{{ old('option_c', $exercise->option_c) }}"
                        required
                    >
                </div>

                {{-- Đáp án D --}}
                <div class="mb-3">
                    <label for="option_d" class="form-label fw-semibold">
                        Đáp án D
                    </label>

                    <input
                        type="text"
                        name="option_d"
                        id="option_d"
                        class="form-control"
                        value="{{ old('option_d', $exercise->option_d) }}"
                        required
                    >
                </div>

                {{-- Đáp án đúng --}}
                <div class="mb-3">
                    <label for="correct_option" class="form-label fw-semibold">
                        Đáp án đúng
                    </label>

                    <select
                        name="correct_option"
                        id="correct_option"
                        class="form-select"
                        required
                    >
                        @foreach ([
                            'a' => 'Đáp án A',
                            'b' => 'Đáp án B',
                            'c' => 'Đáp án C',
                            'd' => 'Đáp án D',
                        ] as $key => $label)

                            <option
                                value="{{ $key }}"
                                @selected(
                                    old(
                                        'correct_option',
                                        $exercise->correct_option
                                    ) === $key
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach
                    </select>
                </div>

                {{-- Giải thích --}}
                <div class="mb-3">
                    <label for="explanation" class="form-label fw-semibold">
                        Giải thích đáp án
                    </label>

                    <textarea
                        name="explanation"
                        id="explanation"
                        class="form-control"
                        rows="4"
                    >{{ old('explanation', $exercise->explanation) }}</textarea>
                </div>

                {{-- Độ khó --}}
                <div class="mb-3">
                    <label for="difficulty" class="form-label fw-semibold">
                        Độ khó
                    </label>

                    <select
                        name="difficulty"
                        id="difficulty"
                        class="form-select"
                        required
                    >
                        @foreach ([
                            'beginner' => 'Cơ bản',
                            'intermediate' => 'Trung bình',
                            'advanced' => 'Nâng cao',
                        ] as $key => $label)

                            <option
                                value="{{ $key }}"
                                @selected(
                                    old(
                                        'difficulty',
                                        $exercise->difficulty
                                    ) === $key
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach
                    </select>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-4">
                    <label for="status" class="form-label fw-semibold">
                        Trạng thái
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select"
                        required
                    >
                        <option
                            value="active"
                            @selected(
                                old('status', $exercise->status) === 'active'
                            )
                        >
                            Đang hoạt động
                        </option>

                        <option
                            value="inactive"
                            @selected(
                                old('status', $exercise->status) === 'inactive'
                            )
                        >
                            Tạm ngưng
                        </option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Lưu thay đổi
                    </button>

                    <a
                        href="{{ route('exercises.show', $exercise) }}"
                        class="btn btn-outline-secondary"
                    >
                        Hủy
                    </a>
                </div>

            </div>
        </div>

    </form>

</div>
@endsection