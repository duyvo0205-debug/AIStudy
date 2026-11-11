@extends('layouts.app')

@section('title', 'Tạo bài tập')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Tạo bài tập
            </h2>

            <p class="text-muted mb-0">
                Tạo bộ câu hỏi để luyện tập và đánh giá kiến thức
            </p>
        </div>

        <a
            href="{{ route('exercises.index') }}"
            class="btn btn-outline-secondary"
        >
            Quay lại
        </a>
    </div>


    {{-- Validation errors --}}
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


    <form
        action="{{ route('exercises.store') }}"
        method="POST"
        id="exerciseForm"
    >

        @csrf


        {{-- =========================================================
             THÔNG TIN BÀI TẬP
        ========================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    Thông tin bài tập
                </h5>
            </div>


            <div class="card-body">

                <div class="row">

                    {{-- Bài học --}}
                    <div class="col-md-8 mb-3">

                        <label class="form-label fw-semibold">
                            Bài học liên quan
                        </label>

                        <select
                            name="learning_content_id"
                            class="form-select"
                        >

                            <option value="">
                                -- Không chọn --
                            </option>

                            @foreach ($contents as $content)

                                <option
                                    value="{{ $content->id }}"
                                    {{ old('learning_content_id') == $content->id ? 'selected' : '' }}
                                >
                                    {{ $content->title }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Độ khó --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label fw-semibold">
                            Độ khó
                        </label>

                        <select
                            name="difficulty"
                            class="form-select"
                            required
                        >

                            <option
                                value="beginner"
                                {{ old('difficulty', 'beginner') === 'beginner' ? 'selected' : '' }}
                            >
                                Cơ bản
                            </option>

                            <option
                                value="intermediate"
                                {{ old('difficulty') === 'intermediate' ? 'selected' : '' }}
                            >
                                Trung bình
                            </option>

                            <option
                                value="advanced"
                                {{ old('difficulty') === 'advanced' ? 'selected' : '' }}
                            >
                                Nâng cao
                            </option>

                        </select>

                    </div>


                    {{-- Tên bài tập --}}
                    <div class="col-12 mb-3">

                        <label class="form-label fw-semibold">

                            Tên bài tập

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="{{ old('title') }}"
                            placeholder="Ví dụ: Kiểm tra kiến thức PHP cơ bản"
                            required
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             DANH SÁCH CÂU HỎI
        ========================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="mb-1">
                            Câu hỏi
                        </h5>

                        <small class="text-muted">
                            Một bài tập có thể chứa nhiều câu hỏi
                        </small>

                    </div>


                    <button
                        type="button"
                        class="btn btn-primary"
                        id="addQuestionBtn"
                    >
                        + Thêm câu hỏi
                    </button>

                </div>

            </div>


            <div class="card-body">

                <div id="questionsContainer"></div>

            </div>

        </div>


        {{-- =========================================================
             BUTTON
        ========================================================== --}}

        <div class="d-flex gap-2 mb-5">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Lưu bài tập
            </button>


            <a
                href="{{ route('exercises.index') }}"
                class="btn btn-light border"
            >
                Hủy
            </a>

        </div>

    </form>

</div>


{{-- =============================================================
     TEMPLATE CÂU HỎI
============================================================== --}}

<template id="questionTemplate">

    <div
        class="question-card border rounded p-4 mb-4"
        data-question-index="__INDEX__"
    >

        {{-- Header câu hỏi --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h5 class="fw-bold mb-1">
                    Câu hỏi <span class="question-number">__NUMBER__</span>
                </h5>

                <small class="text-muted">
                    Thiết lập nội dung và đáp án
                </small>

            </div>


            <button
                type="button"
                class="btn btn-sm btn-outline-danger remove-question-btn"
            >
                Xóa câu hỏi
            </button>

        </div>


        {{-- Nội dung câu hỏi --}}
        <div class="mb-3">

            <label class="form-label fw-semibold">

                Nội dung câu hỏi

                <span class="text-danger">*</span>

            </label>

            <textarea
                name="questions[__INDEX__][question]"
                class="form-control question-input"
                rows="3"
                placeholder="Nhập nội dung câu hỏi..."
                required
            ></textarea>

        </div>


        <div class="row">

            {{-- Loại câu hỏi --}}
            <div class="col-md-8 mb-3">

                <label class="form-label fw-semibold">

                    Loại câu hỏi

                    <span class="text-danger">*</span>

                </label>

                <select
                    name="questions[__INDEX__][question_type]"
                    class="form-select question-type"
                    required
                >

                    <option value="multiple_choice">
                        Trắc nghiệm - 1 đáp án
                    </option>

                    <option value="multiple_select">
                        Trắc nghiệm - nhiều đáp án
                    </option>

                    <option value="true_false">
                        Đúng / Sai
                    </option>

                    <option value="essay">
                        Tự luận
                    </option>

                </select>

            </div>


            {{-- Điểm --}}
            <div class="col-md-4 mb-3">

                <label class="form-label fw-semibold">

                    Điểm

                    <span class="text-danger">*</span>

                </label>

                <input
                    type="number"
                    name="questions[__INDEX__][points]"
                    class="form-control"
                    value="1"
                    min="0.1"
                    max="100"
                    step="0.1"
                    required
                >

            </div>

        </div>


        {{-- =====================================================
             KHU VỰC ĐÁP ÁN
        ====================================================== --}}

        <div class="answer-area">

            {{-- -------------------------------------------------
                 MULTIPLE CHOICE
            -------------------------------------------------- --}}

            <div class="multiple-choice-area">

                <div class="alert alert-light border mb-3">

                    <strong>Đáp án:</strong>

                    Chọn một đáp án đúng.

                </div>


                <div class="row">

                    {{-- A --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Đáp án A
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                A
                            </span>

                            <input
                                type="text"
                                name="questions[__INDEX__][options][a]"
                                class="form-control option-input"
                                placeholder="Nhập đáp án A"
                            >

                        </div>

                    </div>


                    {{-- B --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Đáp án B
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                B
                            </span>

                            <input
                                type="text"
                                name="questions[__INDEX__][options][b]"
                                class="form-control option-input"
                                placeholder="Nhập đáp án B"
                            >

                        </div>

                    </div>


                    {{-- C --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Đáp án C
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                C
                            </span>

                            <input
                                type="text"
                                name="questions[__INDEX__][options][c]"
                                class="form-control option-input"
                                placeholder="Nhập đáp án C"
                            >

                        </div>

                    </div>


                    {{-- D --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold">
                            Đáp án D
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                D
                            </span>

                            <input
                                type="text"
                                name="questions[__INDEX__][options][d]"
                                class="form-control option-input"
                                placeholder="Nhập đáp án D"
                            >

                        </div>

                    </div>

                </div>


                {{-- Đáp án đúng --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Đáp án đúng

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="questions[__INDEX__][correct_answer][]"
                        class="form-select correct-answer-select"
                    >

                        <option value="">
                            -- Chọn đáp án đúng --
                        </option>

                        <option value="a">
                            A
                        </option>

                        <option value="b">
                            B
                        </option>

                        <option value="c">
                            C
                        </option>

                        <option value="d">
                            D
                        </option>

                    </select>

                </div>

            </div>


            {{-- -------------------------------------------------
                 MULTIPLE SELECT
            -------------------------------------------------- --}}

            <div
                class="multiple-select-area"
                style="display: none;"
            >

                <div class="alert alert-info border mb-3">

                    <strong>Nhiều đáp án:</strong>

                    Có thể chọn nhiều đáp án đúng.

                </div>


                <div class="row">

                    @foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)

                        <div class="col-md-6 mb-3">

                            <div class="input-group">

                                <span class="input-group-text">
                                    {{ $label }}
                                </span>

                                <input
                                    type="text"
                                    name="questions[__INDEX__][options][{{ $key }}]"
                                    class="form-control option-input"
                                    placeholder="Nhập đáp án {{ $label }}"
                                >

                            </div>

                        </div>

                    @endforeach

                </div>


                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Các đáp án đúng

                        <span class="text-danger">*</span>

                    </label>


                    <div class="d-flex flex-wrap gap-3">

                        @foreach (['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D'] as $key => $label)

                            <div class="form-check">

                                <input
                                    type="checkbox"
                                    name="questions[__INDEX__][correct_answer][]"
                                    value="{{ $key }}"
                                    class="form-check-input"
                                    id="correct___INDEX___{{ $key }}"
                                >

                                <label
                                    class="form-check-label"
                                    for="correct___INDEX___{{ $key }}"
                                >
                                    Đáp án {{ $label }}
                                </label>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>


            {{-- -------------------------------------------------
                 TRUE / FALSE
            -------------------------------------------------- --}}

            <div
                class="true-false-area"
                style="display: none;"
            >

                <div class="alert alert-warning border mb-3">

                    Chọn đáp án đúng cho câu hỏi.

                </div>


                <div class="mb-3">

                    <label class="form-label fw-semibold">

                        Đáp án đúng

                        <span class="text-danger">*</span>

                    </label>


                    <select
                        name="questions[__INDEX__][correct_answer][]"
                        class="form-select"
                    >

                        <option value="">
                            -- Chọn đáp án --
                        </option>

                        <option value="true">
                            Đúng
                        </option>

                        <option value="false">
                            Sai
                        </option>

                    </select>

                </div>

            </div>


            {{-- -------------------------------------------------
                 ESSAY
            -------------------------------------------------- --}}

            <div
                class="essay-area"
                style="display: none;"
            >

                <div class="alert alert-secondary border mb-3">

                    <strong>Câu tự luận:</strong>

                    Người học sẽ tự nhập câu trả lời.

                    Câu hỏi tự luận hiện tại chưa được
                    tự động chấm điểm.

                </div>

            </div>

        </div>


        {{-- =====================================================
             GIẢI THÍCH
        ====================================================== --}}

        <div class="mb-2">

            <label class="form-label fw-semibold">
                Giải thích đáp án
            </label>

            <textarea
                name="questions[__INDEX__][explanation]"
                class="form-control"
                rows="3"
                placeholder="Giải thích vì sao đáp án đúng..."
            ></textarea>

        </div>

    </div>

</template>


{{-- =============================================================
     JAVASCRIPT
============================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const container =
        document.getElementById('questionsContainer');

    const template =
        document.getElementById('questionTemplate');

    const addButton =
        document.getElementById('addQuestionBtn');

    let questionIndex = 0;


    /**
     * Thêm câu hỏi
     */
    function addQuestion() {

        let html =
            template.innerHTML
                .replaceAll(
                    '__INDEX__',
                    questionIndex
                )
                .replaceAll(
                    '__NUMBER__',
                    questionIndex + 1
                );

        container.insertAdjacentHTML(
            'beforeend',
            html
        );

        const questionCard =
            container.lastElementChild;

        setupQuestionType(
            questionCard
        );

        questionIndex++;

        updateQuestionNumbers();
    }


    /**
     * Cập nhật số thứ tự câu hỏi
     */
    function updateQuestionNumbers() {

        const cards =
            container.querySelectorAll(
                '.question-card'
            );

        cards.forEach(
            function (card, index) {

                const number =
                    card.querySelector(
                        '.question-number'
                    );

                if (number) {
                    number.textContent =
                        index + 1;
                }
            }
        );
    }


    /**
     * Xử lý thay đổi loại câu hỏi
     */
    function setupQuestionType(card) {

        const typeSelect =
            card.querySelector(
                '.question-type'
            );

        if (!typeSelect) {
            return;
        }


        typeSelect.addEventListener(
            'change',
            function () {

                updateAnswerArea(
                    card,
                    this.value
                );

            }
        );


        updateAnswerArea(
            card,
            typeSelect.value
        );
    }


    /**
     * Hiển thị khu vực tương ứng
     */
    function updateAnswerArea(
        card,
        type
    ) {

        const multipleChoice =
            card.querySelector(
                '.multiple-choice-area'
            );

        const multipleSelect =
            card.querySelector(
                '.multiple-select-area'
            );

        const trueFalse =
            card.querySelector(
                '.true-false-area'
            );

        const essay =
            card.querySelector(
                '.essay-area'
            );


        // Ẩn tất cả
        if (multipleChoice) {
            multipleChoice.style.display =
                'none';
        }

        if (multipleSelect) {
            multipleSelect.style.display =
                'none';
        }

        if (trueFalse) {
            trueFalse.style.display =
                'none';
        }

        if (essay) {
            essay.style.display =
                'none';
        }


        // Hiển thị theo loại
        if (
            type === 'multiple_choice'
        ) {

            multipleChoice.style.display =
                'block';

            setRequiredOptions(
                multipleChoice,
                true
            );

        }

        else if (
            type === 'multiple_select'
        ) {

            multipleSelect.style.display =
                'block';

            setRequiredOptions(
                multipleSelect,
                true
            );

        }

        else if (
            type === 'true_false'
        ) {

            trueFalse.style.display =
                'block';

        }

        else if (
            type === 'essay'
        ) {

            essay.style.display =
                'block';

        }
    }


    /**
     * Bật/tắt required cho option
     */
    function setRequiredOptions(
        area,
        enabled
    ) {

        const inputs =
            area.querySelectorAll(
                '.option-input'
            );

        inputs.forEach(
            function (input) {

                input.required =
                    enabled;

            }
        );
    }


    /**
     * Xóa câu hỏi
     */
    container.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.remove-question-btn'
                );

            if (!button) {
                return;
            }


            const cards =
                container.querySelectorAll(
                    '.question-card'
                );


            if (cards.length <= 1) {

                alert(
                    'Bài tập phải có ít nhất một câu hỏi.'
                );

                return;
            }


            const card =
                button.closest(
                    '.question-card'
                );

            card.remove();

            updateQuestionNumbers();
        }
    );


    /**
     * Thêm câu hỏi
     */
    addButton.addEventListener(
        'click',
        function () {

            addQuestion();

        }
    );


    /**
     * Tạo sẵn câu hỏi đầu tiên
     */
    addQuestion();


    /**
     * Kiểm tra trước khi submit
     */
    document
        .getElementById('exerciseForm')
        .addEventListener(
            'submit',
            function (event) {

                const cards =
                    container.querySelectorAll(
                        '.question-card'
                    );


                if (cards.length === 0) {

                    event.preventDefault();

                    alert(
                        'Vui lòng tạo ít nhất một câu hỏi.'
                    );

                    return;
                }


                let valid = true;


                cards.forEach(
                    function (card) {

                        const type =
                            card.querySelector(
                                '.question-type'
                            ).value;


                        /*
                         * Trắc nghiệm 1 đáp án
                         */
                        if (
                            type ===
                            'multiple_choice'
                        ) {

                            const correct =
                                card.querySelector(
                                    '.correct-answer-select'
                                );

                            if (
                                !correct ||
                                !correct.value
                            ) {

                                valid = false;

                            }
                        }


                        /*
                         * Trắc nghiệm nhiều đáp án
                         */
                        if (
                            type ===
                            'multiple_select'
                        ) {

                            const checked =
                                card.querySelectorAll(
                                    'input[type="checkbox"][name*="[correct_answer]"]:checked'
                                );

                            if (
                                checked.length === 0
                            ) {

                                valid = false;

                            }
                        }


                        /*
                         * Đúng / Sai
                         */
                        if (
                            type ===
                            'true_false'
                        ) {

                            const select =
                                card.querySelector(
                                    '.true-false-area select'
                                );

                            if (
                                !select ||
                                !select.value
                            ) {

                                valid = false;

                            }
                        }

                    }
                );


                if (!valid) {

                    event.preventDefault();

                    alert(
                        'Vui lòng chọn đáp án đúng cho tất cả câu hỏi.'
                    );

                }

            }
        );

});

</script>

@endsection