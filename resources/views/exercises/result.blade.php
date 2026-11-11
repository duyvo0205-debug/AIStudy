@extends('layouts.app')

@section('title', 'Kết quả bài tập')

@section('content')

<div class="container-fluid py-4">

    @php
        $totalQuestions = $exercise->questions->count();

        $gradedAttempts = $attempts->filter(
            fn ($attempt) => $attempt->is_correct !== null
        );

        $correctAnswers = $gradedAttempts->where('is_correct', true)->count();

        $gradedCount = $gradedAttempts->count();

        $totalScore = $attempts->sum(
            fn ($attempt) => (float) $attempt->score
        );

        $totalPoints = $exercise->questions->sum(
            fn ($question) => (float) $question->points
        );

        $accuracy = $gradedCount > 0
            ? round(($correctAnswers / $gradedCount) * 100, 2)
            : 0;

        $hasEssay = $exercise->questions->contains(
            fn ($question) => $question->question_type === 'essay'
        );
    @endphp


    {{-- =========================
        HEADER KẾT QUẢ
    ========================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body text-center p-5">

            @if ($gradedCount > 0 && $accuracy >= 80)

                <div style="font-size: 60px;">
                    🎉
                </div>

                <h2 class="fw-bold text-success mt-3">
                    Hoàn thành tốt!
                </h2>

                <p class="text-muted">
                    Bạn đã hoàn thành bài tập với kết quả tốt.
                </p>

            @elseif ($gradedCount > 0 && $accuracy >= 50)

                <div style="font-size: 60px;">
                    📚
                </div>

                <h2 class="fw-bold text-warning mt-3">
                    Đã hoàn thành
                </h2>

                <p class="text-muted">
                    Bạn đã nắm được một phần kiến thức. Hãy tiếp tục ôn tập.
                </p>

            @else

                <div style="font-size: 60px;">
                    💪
                </div>

                <h2 class="fw-bold text-danger mt-3">
                    Cần cố gắng thêm
                </h2>

                <p class="text-muted">
                    Hãy xem lại các câu chưa đúng và thử lại.
                </p>

            @endif


            {{-- =========================
                THỐNG KÊ
            ========================== --}}

            <div class="row justify-content-center mt-4">

                <div class="col-md-3 mb-3">

                    <div class="bg-light rounded p-3">

                        <h2 class="fw-bold mb-1">
                            {{ $correctAnswers }}/{{ $gradedCount }}
                        </h2>

                        <small class="text-muted">
                            Câu đúng
                        </small>

                    </div>

                </div>


                <div class="col-md-3 mb-3">

                    <div class="bg-light rounded p-3">

                        <h2 class="fw-bold mb-1">
                            {{ $accuracy }}%
                        </h2>

                        <small class="text-muted">
                            Độ chính xác
                        </small>

                    </div>

                </div>


                <div class="col-md-3 mb-3">

                    <div class="bg-light rounded p-3">

                        <h2 class="fw-bold mb-1">
                            {{ number_format($totalScore, 2) }}
                        </h2>

                        <small class="text-muted">
                            Tổng điểm
                        </small>

                    </div>

                </div>

            </div>


            @if ($hasEssay)

                <div class="alert alert-info mt-3 mb-0">

                    <i class="bi bi-info-circle me-1"></i>

                    Bài tập có câu tự luận.
                    Những câu này cần được đánh giá riêng và hiện chưa được tính
                    vào độ chính xác.

                </div>

            @endif

        </div>

    </div>



    {{-- =========================
        DANH SÁCH CÂU HỎI
    ========================== --}}

    @foreach ($exercise->questions as $index => $question)

        @php

            $attempt = $attempts->firstWhere(
                'exercise_question_id',
                $question->id
            );

            $userAnswer = $attempt
                ? $attempt->selected_option
                : null;

            $isEssay = $question->question_type === 'essay';

            $isCorrect = $attempt
                ? $attempt->is_correct
                : null;

            $correctAnswer = $question->correct_answer ?? [];

        @endphp


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4">


                {{-- =========================
                    TIÊU ĐỀ CÂU
                ========================== --}}

                <div class="d-flex justify-content-between align-items-start mb-3">

                    <h5 class="fw-bold mb-0">

                        Câu {{ $index + 1 }}

                    </h5>


                    <span class="badge bg-secondary">

                        {{ number_format((float) $question->points, 2) }}
                        điểm

                    </span>

                </div>


                {{-- =========================
                    NỘI DUNG CÂU HỎI
                ========================== --}}

                <div class="mb-4">

                    <p class="fw-semibold mb-2">

                        {{ $question->question }}

                    </p>

                </div>



                {{-- =========================
                    TRẮC NGHIỆM
                ========================== --}}

                @if ($question->question_type === 'multiple_choice')

                    @php

                        $selected = $userAnswer;

                        $correct = $correctAnswer[0] ?? null;

                    @endphp


                    @foreach (($question->options ?? []) as $key => $option)

                        @php

                            $isSelected = $selected === $key;

                            $isCorrectOption = $correct === $key;

                        @endphp


                        <div
                            class="border rounded p-3 mb-2
                            @if ($isCorrectOption)
                                border-success bg-success bg-opacity-10
                            @elseif ($isSelected && !$isCorrectOption)
                                border-danger bg-danger bg-opacity-10
                            @endif"
                        >

                            <div class="d-flex align-items-center">

                                <strong class="me-2">
                                    {{ strtoupper($key) }}.
                                </strong>

                                <span>
                                    {{ $option }}
                                </span>


                                @if ($isCorrectOption)

                                    <span class="badge bg-success ms-auto">
                                        Đáp án đúng
                                    </span>

                                @elseif ($isSelected)

                                    <span class="badge bg-danger ms-auto">
                                        Bạn đã chọn
                                    </span>

                                @endif

                            </div>

                        </div>

                    @endforeach



                {{-- =========================
                    NHIỀU ĐÁP ÁN
                ========================== --}}

                @elseif ($question->question_type === 'multiple_select')

                    @php

                        $selectedAnswers = [];

                        if ($userAnswer) {

                            $decoded = json_decode(
                                $userAnswer,
                                true
                            );

                            if (is_array($decoded)) {
                                $selectedAnswers = $decoded;
                            }

                        }

                        $correctAnswersForQuestion =
                            array_map(
                                'strval',
                                $correctAnswer
                            );

                    @endphp


                    @foreach (($question->options ?? []) as $key => $option)

                        @php

                            $key = (string) $key;

                            $isSelected =
                                in_array(
                                    $key,
                                    $selectedAnswers,
                                    true
                                );

                            $isCorrectOption =
                                in_array(
                                    $key,
                                    $correctAnswersForQuestion,
                                    true
                                );

                        @endphp


                        <div
                            class="border rounded p-3 mb-2
                            @if ($isCorrectOption)
                                border-success bg-success bg-opacity-10
                            @elseif ($isSelected && !$isCorrectOption)
                                border-danger bg-danger bg-opacity-10
                            @endif"
                        >

                            <div class="d-flex align-items-center">

                                <strong class="me-2">
                                    {{ strtoupper($key) }}.
                                </strong>

                                <span>
                                    {{ $option }}
                                </span>


                                @if ($isCorrectOption)

                                    <span class="badge bg-success ms-auto">
                                        Đáp án đúng
                                    </span>

                                @elseif ($isSelected)

                                    <span class="badge bg-danger ms-auto">
                                        Bạn đã chọn
                                    </span>

                                @endif

                            </div>

                        </div>

                    @endforeach



                {{-- =========================
                    ĐÚNG / SAI
                ========================== --}}

                @elseif ($question->question_type === 'true_false')

                    @php

                        $correct =
                            strtolower(
                                (string) ($correctAnswer[0] ?? '')
                            );

                        $selected =
                            strtolower(
                                (string) $userAnswer
                            );

                    @endphp


                    <div class="row">

                        @foreach ([
                            'true' => 'Đúng',
                            'false' => 'Sai'
                        ] as $key => $label)

                            @php

                                $isSelected =
                                    $selected === $key;

                                $isCorrectOption =
                                    $correct === $key;

                            @endphp


                            <div class="col-md-6 mb-2">

                                <div
                                    class="border rounded p-3
                                    @if ($isCorrectOption)
                                        border-success bg-success bg-opacity-10
                                    @elseif ($isSelected && !$isCorrectOption)
                                        border-danger bg-danger bg-opacity-10
                                    @endif"
                                >

                                    <div class="d-flex align-items-center">

                                        <strong class="me-2">
                                            {{ $label }}
                                        </strong>


                                        @if ($isCorrectOption)

                                            <span class="badge bg-success ms-auto">
                                                Đáp án đúng
                                            </span>

                                        @elseif ($isSelected)

                                            <span class="badge bg-danger ms-auto">
                                                Bạn đã chọn
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>



                {{-- =========================
                    TỰ LUẬN
                ========================== --}}

                @elseif ($question->question_type === 'essay')

                    <div class="bg-light rounded p-3">

                        <h6 class="fw-bold">
                            Câu trả lời của bạn
                        </h6>

                        <p class="mb-0">

                            @if ($userAnswer)
                                {{ $userAnswer }}
                            @else
                                <span class="text-muted">
                                    Không có câu trả lời.
                                </span>
                            @endif

                        </p>

                    </div>


                    <div class="alert alert-warning mt-3 mb-0">

                        <strong>
                            Chưa chấm điểm
                        </strong>

                        <br>

                        Câu tự luận cần được đánh giá riêng.

                    </div>

                @endif



                {{-- =========================
                    KẾT QUẢ CÂU
                ========================== --}}

                @if (!$isEssay)

                    <div class="mt-3">

                        @if ($isCorrect)

                            <span class="badge bg-success">
                                ✓ Chính xác
                            </span>

                        @else

                            <span class="badge bg-danger">
                                ✗ Chưa chính xác
                            </span>

                        @endif


                        <span class="badge bg-primary ms-1">

                            +{{ number_format((float) ($attempt->score ?? 0), 2) }}

                            /

                            {{ number_format((float) $question->points, 2) }}

                            điểm

                        </span>

                    </div>

                @endif



                {{-- =========================
                    GIẢI THÍCH
                ========================== --}}

                @if ($question->explanation)

                    <div class="mt-4 p-3 bg-light rounded">

                        <h6 class="fw-bold">
                            💡 Giải thích
                        </h6>

                        <p class="mb-0">
                            {{ $question->explanation }}
                        </p>

                    </div>

                @endif

            </div>

        </div>

    @endforeach



    {{-- =========================
        NÚT ĐIỀU HƯỚNG
    ========================== --}}

    <div class="text-center mt-4 mb-5">

        <a
            href="{{ route('exercises.show', $exercise) }}"
            class="btn btn-primary me-2"
        >
            Làm lại
        </a>

        <a
            href="{{ route('exercises.index') }}"
            class="btn btn-outline-secondary"
        >
            Danh sách bài tập
        </a>

    </div>

</div>

@endsection