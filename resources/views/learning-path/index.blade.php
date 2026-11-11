@extends('layouts.app')

@section('title', 'Lộ trình cá nhân - AIStudy')

@push('styles')
<style>
    .learning-path-page {
        max-width: 1100px;
        margin: 0 auto;
    }

    .learning-path-title {
        margin-bottom: 25px;
    }

    .learning-path-title h1 {
        margin-bottom: 8px;
    }

    .learning-path-title p {
        color: #6b7280;
        margin-bottom: 0;
    }

    .learning-path-card {
        background: #fff;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }

    .learning-path-section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .learning-path-info-item {
        margin-bottom: 10px;
    }

    .learning-path-info-item:last-child {
        margin-bottom: 0;
    }

    .learning-path-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 13px;
    }

    .learning-path-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 15px;
    }

    .learning-path-item:last-child {
        margin-bottom: 0;
    }

    .learning-path-item h4,
    .learning-path-item h5 {
        margin-bottom: 12px;
    }

    .learning-path-item p {
        margin-bottom: 8px;
    }

    .priority-high {
        color: #dc3545;
        font-weight: 700;
    }

    .priority-medium {
        color: #fd7e14;
        font-weight: 700;
    }

    .priority-low {
        color: #198754;
        font-weight: 700;
    }

    .learning-path-reasons {
        margin-top: 8px;
        margin-bottom: 0;
    }

    .learning-path-empty {
        color: #6b7280;
        margin-bottom: 0;
    }

    .path-summary-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        height: 100%;
    }

    .path-summary-icon {
        font-size: 26px;
    }

    .path-summary-title {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .path-summary-number {
        font-size: 26px;
        font-weight: 700;
    }

    .path-stage {
        margin-top: 24px;
    }

    .path-stage-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .path-stage-count {
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
    }

    /* =========================
       AI ĐIỀU CHỈNH LỘ TRÌNH
       ========================= */

    .ai-adjustment-card {
        border: 1px solid #dbeafe;
        background: linear-gradient(
            135deg,
            #eff6ff 0%,
            #ffffff 100%
        );
    }

    .ai-adjustment-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 50%;
        background: #dbeafe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
    }

    .ai-adjustment-title {
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .ai-adjustment-description {
        color: #6b7280;
        margin-bottom: 0;
        line-height: 1.6;
    }

    .ai-adjustment-focus {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        background: #fff3cd;
        color: #664d03;
        font-size: 13px;
        font-weight: 600;
    }

    /* =========================
       NỘI DUNG TIẾP THEO
       ========================= */

    .next-content-card {
        border: 1px solid #dbeafe;
        background: linear-gradient(
            135deg,
            #ffffff 0%,
            #f8fbff 100%
        );
    }

    .next-content-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 50%;
        background: #e0edff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }

    .next-content-title {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
    }

    .next-content-description {
        color: #64748b;
        margin-top: 12px;
        margin-bottom: 0;
        line-height: 1.6;
    }

    .next-content-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        background: #dbeafe;
        color: #2563eb;
        font-size: 14px;
        font-weight: 600;
    }

    /* =========================
       BƯỚC TIẾP THEO
       ========================= */

    .upcoming-content-card {
        border: 1px solid #e5e7eb;
        background: linear-gradient(
            135deg,
            #ffffff 0%,
            #f8fafc 100%
        );
    }

    .upcoming-content-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        flex-shrink: 0;
    }

    .upcoming-content-title {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
    }

    .upcoming-content-description {
        color: #64748b;
        margin-top: 10px;
        margin-bottom: 0;
        line-height: 1.6;
    }

    .upcoming-content-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
    }
</style>
@endpush


@section('content')

<div class="learning-path-page">

    {{-- =========================================================
         TIÊU ĐỀ
         ========================================================= --}}

    <div class="learning-path-title">

        <h1>
            Lộ trình cá nhân
        </h1>

        <p>
            Lộ trình học tập được xây dựng dựa trên thông tin,
            mục tiêu và kết quả học tập của bạn.
        </p>

    </div>


    {{-- =========================================================
         AI ĐIỀU CHỈNH LỘ TRÌNH
         ========================================================= --}}

    @if(!empty($personalization['adjustment']))

        <div class="learning-path-card ai-adjustment-card">

            <div class="d-flex align-items-start gap-3">

                <div class="ai-adjustment-icon">
                    🤖
                </div>

                <div class="flex-grow-1">

                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">

                        <div>

                            <span class="badge bg-primary mb-2">
                                AI điều chỉnh lộ trình
                            </span>

                            <div class="ai-adjustment-title">
                                {{ $personalization['adjustment']['title'] }}
                            </div>

                        </div>

                        @if(!empty($personalization['adjustment']['focus']))

                            <span class="ai-adjustment-focus">

                                Nội dung:
                                {{ $personalization['adjustment']['focus'] }}

                            </span>

                        @endif

                    </div>

                    <p class="ai-adjustment-description">

                        {{ $personalization['adjustment']['description'] }}

                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
         NỘI DUNG TIẾP THEO DO AI XÁC ĐỊNH
         Chỉ xuất hiện khi PersonalizationService trả về
         next_content.
         ========================================================= --}}

    @if(!empty($personalization['next_content']))

        @php
            $nextContent = $personalization['next_content'];
        @endphp

        <div class="learning-path-card next-content-card">

            <div class="d-flex align-items-start gap-3">

                <div class="next-content-icon">
                    🚀
                </div>

                <div class="flex-grow-1">

                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">

                        <div>

                            <span class="next-content-badge">
                                Nội dung tiếp theo
                            </span>

                            <div class="next-content-title">
                                {{ $nextContent['title'] }}
                            </div>

                        </div>

                        @if($nextContent['status'] === 'pending')

                            <form
                                action="{{ route('learning.start', $nextContent['content_id']) }}"
                                method="POST"
                                class="d-inline"
                            >

                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    🚀 Bắt đầu học
                                </button>

                            </form>

                        @else

                            <a
                                href="{{ route('learning.show', $nextContent['content_id']) }}"
                                class="btn btn-primary"
                            >
                                Tiếp tục học
                            </a>

                        @endif

                    </div>

                    <p class="next-content-description">

                        Kết quả học tập hiện tại cho thấy bạn đã có thể
                        tiếp tục sang nội dung mới trong lộ trình.

                    </p>

                    @if(!empty($nextContent['subject']))

                        <p class="mb-1 mt-3">

                            <strong>Môn học:</strong>

                            {{ $nextContent['subject'] }}

                        </p>

                    @endif

                    @if(!empty($nextContent['difficulty']))

                        <p class="mb-0">

                            <strong>Độ khó:</strong>

                            {{ $nextContent['difficulty'] }}

                        </p>

                    @endif

                </div>

            </div>

        </div>

    @else

        {{-- =====================================================
             BƯỚC TIẾP THEO ĐỊNH HƯỚNG
             Khi người học chưa đủ điều kiện chuyển sang
             next_content chính thức, hiển thị nội dung pending
             tiếp theo để người học biết hướng đi.
             ===================================================== --}}

        @php
            $upcomingContent = null;

            if (
                !empty($personalization['learning_path']['not_started'])
            ) {
                $upcomingContent =
                    $personalization['learning_path']['not_started'][0];
            }
        @endphp

        @if($upcomingContent)

            <div class="learning-path-card upcoming-content-card">

                <div class="d-flex align-items-start gap-3">

                    <div class="upcoming-content-icon">
                        🧭
                    </div>

                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">

                            <div>

                                <span class="upcoming-content-badge">
                                    Bước tiếp theo trong lộ trình
                                </span>

                                <div class="upcoming-content-title">
                                    {{ $upcomingContent['title'] }}
                                </div>

                            </div>

                            <a
                                href="{{ route('learning.show', $upcomingContent['content_id']) }}"
                                class="btn btn-outline-primary"
                            >
                                Xem nội dung
                            </a>

                        </div>

                        <p class="upcoming-content-description">

                            Đây là nội dung được đặt tiếp theo trong
                            lộ trình học của bạn. Hệ thống hiện đang
                            ưu tiên bạn cải thiện nội dung đang học
                            trước khi chuyển sang nội dung này.

                        </p>

                        @if(!empty($upcomingContent['subject']))

                            <p class="mb-1 mt-3">

                                <strong>Môn học:</strong>

                                {{ $upcomingContent['subject'] }}

                            </p>

                        @endif

                        @if(!empty($upcomingContent['difficulty']))

                            <p class="mb-0">

                                <strong>Độ khó:</strong>

                                {{ $upcomingContent['difficulty'] }}

                            </p>

                        @endif

                    </div>

                </div>

            </div>

        @endif

    @endif


    {{-- =========================================================
         THÔNG TIN NGƯỜI HỌC
         ========================================================= --}}

    <div class="learning-path-card">

        <div class="learning-path-section-title">
            👤 Thông tin người học
        </div>

        <div class="learning-path-info-item">

            <strong>Họ tên:</strong>

            {{ $personalization['learner']['name'] }}

        </div>

        <div class="learning-path-info-item">

            <strong>Trình độ:</strong>

            <span class="learning-path-badge">

                {{ $personalization['learner']['learning_level'] }}

            </span>

        </div>

        <div class="learning-path-info-item">

            <strong>Thời gian học mỗi ngày:</strong>

            {{ $personalization['learner']['daily_study_target'] }}

            phút

        </div>

        @if(
            $personalization['learner']['preferred_study_start']
            &&
            $personalization['learner']['preferred_study_end']
        )

            <div class="learning-path-info-item">

                <strong>Khung giờ học ưu tiên:</strong>

                {{ $personalization['learner']['preferred_study_start'] }}

                -

                {{ $personalization['learner']['preferred_study_end'] }}

            </div>

        @endif

    </div>


    {{-- =========================================================
         MỤC TIÊU HỌC TẬP
         ========================================================= --}}

    <div class="learning-path-card">

        <div class="learning-path-section-title">
            🎯 Mục tiêu học tập
        </div>

        @forelse($personalization['goals'] as $goal)

            <div class="learning-path-item">

                <h5>
                    {{ $goal->title }}
                </h5>

                @if($goal->subject)

                    <p>
                        <strong>Môn học:</strong>

                        {{ $goal->subject }}
                    </p>

                @endif

                @if($goal->description)

                    <p>
                        {{ $goal->description }}
                    </p>

                @endif

                <p>

                    <strong>Trình độ hiện tại:</strong>

                    {{ $goal->current_level }}

                </p>

                <p>

                    <strong>Trình độ mục tiêu:</strong>

                    {{ $goal->target_level }}

                </p>

                @if($goal->target_date)

                    <p class="mb-0">

                        <strong>Thời hạn:</strong>

                        {{ $goal->target_date->format('d/m/Y') }}

                    </p>

                @endif

            </div>

        @empty

            <p class="learning-path-empty">

                Bạn chưa có mục tiêu học tập đang hoạt động.

            </p>

        @endforelse

    </div>


    {{-- =========================================================
         NỘI DUNG ĐƯỢC ƯU TIÊN
         ========================================================= --}}

    <div class="learning-path-card">

        <div class="learning-path-section-title">
            ⭐ Nội dung được ưu tiên
        </div>

        @if($personalization['recommendation'])

            @php
                $recommendation =
                    $personalization['recommendation'];
            @endphp

            <div class="learning-path-item">

                <h4>
                    {{ $recommendation['title'] }}
                </h4>

                <p>

                    <strong>Môn học:</strong>

                    {{ $recommendation['subject'] }}

                </p>

                <p>

                    <strong>Độ khó:</strong>

                    {{ $recommendation['difficulty'] }}

                </p>

                <p>

                    <strong>Trạng thái:</strong>

                    {{ $recommendation['status'] }}

                </p>

                <p>

                    <strong>Mức ưu tiên:</strong>

                    @if($recommendation['priority'] === 'Cao')

                        <span class="priority-high">
                            Cao
                        </span>

                    @elseif($recommendation['priority'] === 'Trung bình')

                        <span class="priority-medium">
                            Trung bình
                        </span>

                    @else

                        <span class="priority-low">
                            Thấp
                        </span>

                    @endif

                </p>

                <p>

                    <strong>Điểm ưu tiên:</strong>

                    {{ $recommendation['score'] }}

                </p>

                @if(!empty($recommendation['result']))

                    <p>

                        <strong>Độ chính xác hiện tại:</strong>

                        {{ $recommendation['result']['accuracy'] }}%

                    </p>

                    <p>

                        <strong>Đánh giá:</strong>

                        {{ $recommendation['result']['level'] }}

                    </p>

                @endif

                <strong>
                    Lý do đề xuất:
                </strong>

                <ul class="learning-path-reasons">

                    @foreach($recommendation['reasons'] as $reason)

                        <li>
                            {{ $reason }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @else

            <p class="learning-path-empty">

                Hiện tại chưa có nội dung phù hợp để đề xuất.

            </p>

        @endif

    </div>


    {{-- =========================================================
         LỘ TRÌNH HỌC TẬP
         ========================================================= --}}

    <div class="learning-path-card">

        <div class="learning-path-section-title">
            📚 Lộ trình học tập
        </div>

        <p class="text-muted mb-4">

            Hệ thống phân loại nội dung học tập dựa trên
            trạng thái học và kết quả bài tập của bạn.

        </p>


        {{-- =====================================================
             THỐNG KÊ
             ===================================================== --}}

        <div class="row g-3 mb-4">

            {{-- CHƯA BẮT ĐẦU --}}
            <div class="col-md-6 col-lg-4">

                <div class="path-summary-card">

                    <div class="path-summary-icon">
                        📖
                    </div>

                    <div>

                        <div class="path-summary-title">
                            Chưa bắt đầu
                        </div>

                        <div class="path-summary-number">

                            {{ $personalization['learning_path_summary']['not_started'] }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- ĐANG HỌC --}}
            <div class="col-md-6 col-lg-4">

                <div class="path-summary-card">

                    <div class="path-summary-icon">
                        ▶️
                    </div>

                    <div>

                        <div class="path-summary-title">
                            Đang học
                        </div>

                        <div class="path-summary-number">

                            {{ $personalization['learning_path_summary']['in_progress'] }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- CẦN CẢI THIỆN --}}
            <div class="col-md-6 col-lg-4">

                <div class="path-summary-card">

                    <div class="path-summary-icon">
                        ⚠️
                    </div>

                    <div>

                        <div class="path-summary-title">
                            Cần cải thiện
                        </div>

                        <div class="path-summary-number">

                            {{ $personalization['learning_path_summary']['needs_improvement'] }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- CẦN CỦNG CỐ --}}
            <div class="col-md-6 col-lg-4">

                <div class="path-summary-card">

                    <div class="path-summary-icon">
                        🔄
                    </div>

                    <div>

                        <div class="path-summary-title">
                            Cần củng cố
                        </div>

                        <div class="path-summary-number">

                            {{ $personalization['learning_path_summary']['needs_review'] }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- HOÀN THÀNH --}}
            <div class="col-md-6 col-lg-4">

                <div class="path-summary-card">

                    <div class="path-summary-icon">
                        ✅
                    </div>

                    <div>

                        <div class="path-summary-title">
                            Đã hoàn thành
                        </div>

                        <div class="path-summary-number">

                            {{ $personalization['learning_path_summary']['completed'] }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             CHƯA BẮT ĐẦU
             ===================================================== --}}

        @if(
            count(
                $personalization['learning_path']['not_started']
            ) > 0
        )

            <div class="path-stage">

                <div class="path-stage-title">

                    <span>
                        📖 Chưa bắt đầu
                    </span>

                    <span class="path-stage-count">

                        {{ count($personalization['learning_path']['not_started']) }}

                        nội dung

                    </span>

                </div>


                @foreach(
                    $personalization['learning_path']['not_started']
                    as $item
                )

                    <div class="learning-path-item">

                        <h5>

                            <a
                                href="{{ route('learning.show', $item['content_id']) }}"
                                class="text-decoration-none"
                            >
                                {{ $item['title'] }}
                            </a>

                        </h5>

                        <p>

                            <strong>Môn học:</strong>

                            {{ $item['subject'] }}

                        </p>

                        <p>

                            <strong>Độ khó:</strong>

                            {{ $item['difficulty'] }}

                        </p>

                        <p>

                            <strong>Mức ưu tiên:</strong>

                            {{ $item['priority'] }}

                        </p>

                        @if(!empty($item['reasons']))

                            <strong>
                                Lý do:
                            </strong>

                            <ul class="learning-path-reasons">

                                @foreach($item['reasons'] as $reason)

                                    <li>
                                        {{ $reason }}
                                    </li>

                                @endforeach

                            </ul>

                        @endif

                    </div>

                @endforeach

            </div>

        @endif


        {{-- =====================================================
             ĐANG HỌC
             ===================================================== --}}

        @if(
            count(
                $personalization['learning_path']['in_progress']
            ) > 0
        )

            <div class="path-stage">

                <div class="path-stage-title">

                    <span>
                        ▶️ Đang học
                    </span>

                    <span class="path-stage-count">

                        {{ count($personalization['learning_path']['in_progress']) }}

                        nội dung

                    </span>

                </div>


                @foreach(
                    $personalization['learning_path']['in_progress']
                    as $item
                )

                    <div class="learning-path-item">

                        <h5>

                            <a
                                href="{{ route('learning.show', $item['content_id']) }}"
                                class="text-decoration-none"
                            >
                                {{ $item['title'] }}
                            </a>

                        </h5>

                        <p>

                            <strong>Môn học:</strong>

                            {{ $item['subject'] }}

                        </p>

                        <p>

                            <strong>Độ khó:</strong>

                            {{ $item['difficulty'] }}

                        </p>

                        @if($item['result'])

                            <p>

                                <strong>Độ chính xác:</strong>

                                {{ $item['result']['accuracy'] }}%

                            </p>

                        @endif

                    </div>

                @endforeach

            </div>

        @endif


        {{-- =====================================================
             CẦN CẢI THIỆN
             ===================================================== --}}

        @if(
            count(
                $personalization['learning_path']['needs_improvement']
            ) > 0
        )

            <div class="path-stage">

                <div class="path-stage-title">

                    <span>
                        ⚠️ Cần cải thiện
                    </span>

                    <span class="path-stage-count">

                        {{ count($personalization['learning_path']['needs_improvement']) }}

                        nội dung

                    </span>

                </div>


                @foreach(
                    $personalization['learning_path']['needs_improvement']
                    as $item
                )

                    <div class="learning-path-item">

                        <h5>

                            <a
                                href="{{ route('learning.show', $item['content_id']) }}"
                                class="text-decoration-none"
                            >
                                {{ $item['title'] }}
                            </a>

                        </h5>

                        <p>

                            <strong>Độ chính xác:</strong>

                            {{ $item['result']['accuracy'] }}%

                        </p>

                        <p>

                            <strong>Đánh giá:</strong>

                            {{ $item['result']['level'] }}

                        </p>

                        <p class="mb-0">

                            Hệ thống đề xuất bạn dành
                            thêm thời gian cho nội dung này.

                        </p>

                    </div>

                @endforeach

            </div>

        @endif


        {{-- =====================================================
             CẦN CỦNG CỐ
             ===================================================== --}}

        @if(
            count(
                $personalization['learning_path']['needs_review']
            ) > 0
        )

            <div class="path-stage">

                <div class="path-stage-title">

                    <span>
                        🔄 Cần củng cố
                    </span>

                    <span class="path-stage-count">

                        {{ count($personalization['learning_path']['needs_review']) }}

                        nội dung

                    </span>

                </div>


                @foreach(
                    $personalization['learning_path']['needs_review']
                    as $item
                )

                    <div class="learning-path-item">

                        <h5>

                            <a
                                href="{{ route('learning.show', $item['content_id']) }}"
                                class="text-decoration-none"
                            >
                                {{ $item['title'] }}
                            </a>

                        </h5>

                        <p>

                            <strong>Độ chính xác:</strong>

                            {{ $item['result']['accuracy'] }}%

                        </p>

                        <p>

                            <strong>Đánh giá:</strong>

                            {{ $item['result']['level'] }}

                        </p>

                        <p class="mb-0">

                            Hệ thống đề xuất ôn tập lại
                            nội dung này.

                        </p>

                    </div>

                @endforeach

            </div>

        @endif


        {{-- =====================================================
             HOÀN THÀNH
             ===================================================== --}}

        @if(
            count(
                $personalization['learning_path']['completed']
            ) > 0
        )

            <div class="path-stage">

                <div class="path-stage-title">

                    <span>
                        ✅ Đã hoàn thành
                    </span>

                    <span class="path-stage-count">

                        {{ count($personalization['learning_path']['completed']) }}

                        nội dung

                    </span>

                </div>


                @foreach(
                    $personalization['learning_path']['completed']
                    as $item
                )

                    <div class="learning-path-item">

                        <h5>

                            <a
                                href="{{ route('learning.show', $item['content_id']) }}"
                                class="text-decoration-none"
                            >
                                {{ $item['title'] }}
                            </a>

                        </h5>

                        <p class="mb-0">

                            Nội dung đã được hoàn thành.

                        </p>

                    </div>

                @endforeach

            </div>

        @endif

    </div>


    {{-- =========================================================
         KẾT QUẢ HỌC TẬP
         ========================================================= --}}

    <div class="learning-path-card">

        <div class="learning-path-section-title">
            📊 Kết quả học tập
        </div>

        @forelse(
            $personalization['results_by_content']
            as $result
        )

            <div class="learning-path-item">

                <h5>
                    {{ $result['title'] }}
                </h5>

                <p>

                    <strong>Số lần làm:</strong>

                    {{ $result['total'] }}

                </p>

                <p>

                    <strong>Đúng:</strong>

                    {{ $result['correct'] }}

                </p>

                <p>

                    <strong>Sai:</strong>

                    {{ $result['wrong'] }}

                </p>

                <p>

                    <strong>Độ chính xác:</strong>

                    {{ $result['accuracy'] }}%

                </p>

                <p class="mb-0">

                    <strong>Đánh giá:</strong>

                    {{ $result['level'] }}

                </p>

            </div>

        @empty

            <p class="learning-path-empty">

                Chưa có dữ liệu kết quả bài tập.

            </p>

        @endforelse

    </div>

</div>

@endsection