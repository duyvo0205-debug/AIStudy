@extends('layouts.app')

@section('title', 'Làm bài tập')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                {{ $exercise->title }}
            </h2>

            <p class="text-muted mb-0">
                Làm bài và kiểm tra kiến thức
            </p>
        </div>

        <a
            href="{{ route('exercises.index') }}"
            class="btn btn-outline-secondary"
        >
            Quay lại
        </a>

    </div>


    {{-- Thông tin bài tập --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">
                    <small class="text-muted d-block">
                        Độ khó
                    </small>

                    <strong>
                        @switch($exercise->difficulty)

                            @case('beginner')
                                Cơ bản
                                @break

                            @case('intermediate')
                                Trung bình
                                @break

                            @case('advanced')
                                Nâng cao
                                @break

                            @default
                                Chưa xác định

                        @endswitch
                    </strong>
                </div>


                <div class="col-md-4">
                    <small class="text-muted d-block">
                        Số câu hỏi
                    </small>

                    <strong>
                        {{ $exercise->questions->count() }} câu
                    </strong>
                </div>


                <div class="col-md-4">
                    <small class="text-muted d-block">
                        Tổng điểm
                    </small>

                    <strong>
                        {{ number_format($exercise->questions->sum('points'), 1) }}
                    </strong>
                </div>

            </div>

        </div>

    </div>


    {{-- Không có câu hỏi --}}
    @if ($exercise->questions->isEmpty())

        <div class="alert alert-warning">

            Bài tập này chưa có câu hỏi.

        </div>

    @else


        {{-- Form làm bài --}}
        <form
            action="{{ route('exercises.submit', $exercise) }}"
            method="POST"
            id="exerciseForm"
        >

            @csrf


            @foreach ($exercise->questions as $index => $question)

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-body p-4">


                        {{-- Số câu --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <span class="badge bg-primary">
                                Câu {{ $index + 1 }}
                            </span>

                            <span class="badge bg-light text-dark border">
                                {{ number_format($question->points, 1) }} điểm
                            </span>

                        </div>


                        {{-- Nội dung --}}
                        <h5 class="fw-bold mb-4">
                            {{ $question->question }}
                        </h5>


                        {{-- =================================================
                             TRẮC NGHIỆM 1 ĐÁP ÁN
                        ================================================== --}}

                        @if ($question->question_type === 'multiple_choice')

                            @php
                                $options = $question->options ?? [];
                            @endphp


                            <div class="d-flex flex-column gap-3">

                                @foreach ($options as $key => $option)

                                    <label
                                        class="answer-option border rounded p-3"
                                        style="cursor: pointer;"
                                    >

                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $key }}"
                                            class="form-check-input me-2"
                                            required
                                        >

                                        <strong>
                                            {{ strtoupper($key) }}.
                                        </strong>

                                        {{ $option }}

                                    </label>

                                @endforeach

                            </div>


                        {{-- =================================================
                             TRẮC NGHIỆM NHIỀU ĐÁP ÁN
                        ================================================== --}}

                        @elseif ($question->question_type === 'multiple_select')

                            @php
                                $options = $question->options ?? [];
                            @endphp


                            <div class="alert alert-info">
                                Câu hỏi này có thể có
                                <strong>nhiều đáp án đúng</strong>.
                            </div>


                            <div class="d-flex flex-column gap-3">

                                @foreach ($options as $key => $option)

                                    <label
                                        class="answer-option border rounded p-3"
                                        style="cursor: pointer;"
                                    >

                                        <input
                                            type="checkbox"
                                            name="answers[{{ $question->id }}][]"
                                            value="{{ $key }}"
                                            class="form-check-input me-2"
                                        >

                                        <strong>
                                            {{ strtoupper($key) }}.
                                        </strong>

                                        {{ $option }}

                                    </label>

                                @endforeach

                            </div>


                        {{-- =================================================
                             ĐÚNG / SAI
                        ================================================== --}}

                        @elseif ($question->question_type === 'true_false')

                            <div class="d-flex flex-column gap-3">

                                <label
                                    class="answer-option border rounded p-3"
                                    style="cursor: pointer;"
                                >

                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="true"
                                        class="form-check-input me-2"
                                        required
                                    >

                                    <strong>
                                        Đúng
                                    </strong>

                                </label>


                                <label
                                    class="answer-option border rounded p-3"
                                    style="cursor: pointer;"
                                >

                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="false"
                                        class="form-check-input me-2"
                                        required
                                    >

                                    <strong>
                                        Sai
                                    </strong>

                                </label>

                            </div>


                        {{-- =================================================
                             TỰ LUẬN
                        ================================================== --}}

                        @elseif ($question->question_type === 'essay')

                            <div class="alert alert-secondary">

                                Đây là câu hỏi tự luận.
                                Hãy trình bày câu trả lời của bạn
                                một cách đầy đủ.

                            </div>


                            <textarea
                                name="answers[{{ $question->id }}]"
                                class="form-control"
                                rows="6"
                                placeholder="Nhập câu trả lời của bạn..."
                                required
                            ></textarea>

                        @endif


                        {{-- Validation lỗi theo câu --}}
                        @error('answers.' . $question->id)

                            <div class="text-danger mt-3">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            @endforeach


            {{-- Nộp bài --}}
            <div class="card border-0 shadow-sm mb-5">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h5 class="fw-bold mb-1">
                                Hoàn thành bài tập
                            </h5>

                            <p class="text-muted mb-0">
                                Kiểm tra lại câu trả lời trước khi nộp.
                            </p>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary px-4"
                        >
                            Nộp bài
                        </button>

                    </div>

                </div>

            </div>

        </form>

    @endif

</div>


{{-- CSS --}}
<style>

    .answer-option {
        transition: all 0.2s ease;
        background-color: #fff;
    }

    .answer-option:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd !important;
    }

    .answer-option:has(input:checked) {
        background-color: #eef5ff;
        border-color: #0d6efd !important;
    }

</style>

@endsection