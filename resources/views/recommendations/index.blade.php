@extends('layouts.app')

@section('title', 'Đề xuất của AI')

@section('content')

<div class="container-fluid py-4">

    {{-- =========================================================
        THÔNG BÁO
    ========================================================== --}}

    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">

            <strong>✓ Thành công!</strong>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if (session('error'))

        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">

            <strong>⚠ Có lỗi!</strong>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =========================================================
        TIÊU ĐỀ
    ========================================================== --}}

    <div class="mb-4">

        <h2 class="fw-bold mb-1">
            Đề xuất của AI
        </h2>

        <p class="text-muted mb-0">
            Hệ thống phân tích quá trình học tập để đưa ra nội dung
            và lịch học phù hợp với bạn.
        </p>

    </div>


    {{-- =========================================================
        1. AI ĐỀ XUẤT LỊCH HỌC
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>

                    <h5 class="fw-bold mb-1">
                        📅 AI đề xuất lịch học
                    </h5>

                    <p class="text-muted mb-0">
                        Dựa trên thời khóa biểu, mục tiêu và kết quả học tập của bạn.
                    </p>

                </div>

                <span class="badge bg-primary">
                    {{ $aiRecommendations->count() }} đề xuất chờ xử lý
                </span>

            </div>

        </div>


        <div class="card-body px-4">

            @if ($aiRecommendations->count() > 0)

                @foreach ($aiRecommendations as $aiRecommendation)

                    <div class="border rounded-3 p-4 mb-3">

                        <div class="row">

                            {{-- =================================================
                                THÔNG TIN ĐỀ XUẤT
                            ================================================== --}}

                            <div class="col-lg-8">

                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">

                                    <h5 class="fw-bold mb-0">
                                        {{ $aiRecommendation->title }}
                                    </h5>

                                    <span class="badge bg-warning text-dark">
                                        Chờ xác nhận
                                    </span>

                                </div>


                                {{-- Môn học --}}

                                <div class="mb-3">

                                    <span class="badge bg-light text-dark border me-1">
                                        📚 {{ $aiRecommendation->subject }}
                                    </span>

                                    @if ($aiRecommendation->schoolSchedule)

                                        @if ($aiRecommendation->schoolSchedule->course_code)

                                            <span class="badge bg-light text-dark border me-1">
                                                {{ $aiRecommendation->schoolSchedule->course_code }}
                                            </span>

                                        @endif

                                        @if ($aiRecommendation->schoolSchedule->class_code)

                                            <span class="badge bg-light text-dark border">
                                                Lớp {{ $aiRecommendation->schoolSchedule->class_code }}
                                            </span>

                                        @endif

                                    @endif

                                </div>


                                {{-- Ngày giờ --}}

                                <div class="row mb-3">

                                    <div class="col-md-4 mb-2 mb-md-0">

                                        <div class="bg-light rounded p-3">

                                            <small class="text-muted d-block">
                                                Ngày học
                                            </small>

                                            <strong>
                                                {{ $aiRecommendation->recommended_date?->format('d/m/Y') }}
                                            </strong>

                                        </div>

                                    </div>


                                    <div class="col-md-4 mb-2 mb-md-0">

                                        <div class="bg-light rounded p-3">

                                            <small class="text-muted d-block">
                                                Thời gian
                                            </small>

                                            <strong>

                                                {{ \Carbon\Carbon::parse(
                                                    $aiRecommendation->recommended_start_time
                                                )->format('H:i') }}

                                                -

                                                {{ \Carbon\Carbon::parse(
                                                    $aiRecommendation->recommended_end_time
                                                )->format('H:i') }}

                                            </strong>

                                        </div>

                                    </div>


                                    <div class="col-md-4">

                                        <div class="bg-light rounded p-3">

                                            <small class="text-muted d-block">
                                                Thời lượng
                                            </small>

                                            <strong>
                                                {{ $aiRecommendation->duration_minutes }}
                                                phút
                                            </strong>

                                        </div>

                                    </div>

                                </div>


                                {{-- Mục tiêu --}}

                                @if ($aiRecommendation->learning_goal)

                                    <div class="mb-3">

                                        <strong>
                                            🎯 Mục tiêu:
                                        </strong>

                                        <span class="text-muted">
                                            {{ $aiRecommendation->learning_goal }}
                                        </span>

                                    </div>

                                @endif


                                {{-- Mô tả --}}

                                @if ($aiRecommendation->description)

                                    <p class="text-muted mb-3">
                                        {{ $aiRecommendation->description }}
                                    </p>

                                @endif


                                {{-- Lý do AI --}}

                                @if ($aiRecommendation->reason)

                                    <div class="bg-primary bg-opacity-10 rounded p-3">

                                        <div class="fw-bold text-primary mb-1">
                                            ✦ Vì sao AI đề xuất?
                                        </div>

                                        <div class="small text-muted">
                                            {{ $aiRecommendation->reason }}
                                        </div>

                                    </div>

                                @endif

                            </div>


                            {{-- =================================================
                                NÚT XỬ LÝ
                            ================================================== --}}

                            <div class="col-lg-4 mt-4 mt-lg-0">

                                <div class="h-100 d-flex flex-column justify-content-center align-items-lg-end">

                                    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">

                                        {{-- Chấp nhận --}}

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'recommendations.accept',
                                                $aiRecommendation
                                            ) }}"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-success"
                                            >
                                                ✓ Chấp nhận
                                            </button>

                                        </form>


                                        {{-- Từ chối --}}

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'recommendations.reject',
                                                $aiRecommendation
                                            ) }}"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger"
                                            >
                                                ✕ Từ chối
                                            </button>

                                        </form>

                                    </div>


                                    <small class="text-muted text-lg-end mt-3">

                                        Lịch học chỉ được thêm vào
                                        lịch cá nhân sau khi bạn chấp nhận.

                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            @else

                <div class="text-center py-5">

                    <div style="font-size: 50px;">
                        📅
                    </div>

                    <h6 class="fw-bold mt-3">
                        Chưa có đề xuất lịch học
                    </h6>

                    <p class="text-muted mb-0">
                        Hệ thống sẽ tạo đề xuất dựa trên thời khóa biểu
                        và quá trình học tập của bạn.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        2. ĐIỀU CHỈNH CỦA AI
    ========================================================== --}}

    @if (!empty($adjustment))

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4">

                <div class="d-flex align-items-start">

                    <div
                        class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                        style="width: 50px; height: 50px; font-size: 22px;"
                    >
                        ✦
                    </div>

                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-center flex-wrap">

                            <h5 class="fw-bold mb-1">
                                {{ $adjustment['title'] }}
                            </h5>

                            <span class="badge bg-primary">
                                AI phân tích
                            </span>

                        </div>

                        <p class="text-muted mb-2">
                            {{ $adjustment['description'] }}
                        </p>

                        @if (!empty($adjustment['focus']))

                            <div class="small">

                                <strong>
                                    Nội dung cần tập trung:
                                </strong>

                                <span class="text-primary">
                                    {{ $adjustment['focus'] }}
                                </span>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        3. NỘI DUNG ĐƯỢC ƯU TIÊN
    ========================================================== --}}

    @if ($recommendation)

        @php

            $recommendationResult =
                $recommendation['result'] ?? null;

        @endphp


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-1">
                            🎯 Nội dung được ưu tiên
                        </h5>

                        <p class="text-muted mb-0">
                            Đây là nội dung hệ thống đề xuất bạn tập trung trước.
                        </p>

                    </div>


                    @if (($recommendation['priority'] ?? '') === 'Cao')

                        <span class="badge bg-danger">
                            Ưu tiên cao
                        </span>

                    @elseif (($recommendation['priority'] ?? '') === 'Trung bình')

                        <span class="badge bg-warning text-dark">
                            Ưu tiên trung bình
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            Ưu tiên thấp
                        </span>

                    @endif

                </div>

            </div>


            <div class="card-body px-4 pb-4">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <h3 class="fw-bold">
                            {{ $recommendation['title'] }}
                        </h3>


                        <div class="mb-3">

                            @if (!empty($recommendation['subject']))

                                <span class="badge bg-light text-dark border me-1">
                                    {{ $recommendation['subject'] }}
                                </span>

                            @endif


                            @if (!empty($recommendation['difficulty']))

                                <span class="badge bg-light text-dark border">
                                    Độ khó:
                                    {{ $recommendation['difficulty'] }}
                                </span>

                            @endif

                        </div>


                        @if (!empty($recommendation['description']))

                            <p class="text-muted">
                                {{ $recommendation['description'] }}
                            </p>

                        @endif


                        @if ($recommendationResult)

                            <div class="row mt-3">

                                <div class="col-md-4 mb-2">

                                    <div class="bg-light rounded p-3">

                                        <small class="text-muted d-block">
                                            Kết quả
                                        </small>

                                        <strong class="fs-5">
                                            {{ $recommendationResult['accuracy'] }}%
                                        </strong>

                                    </div>

                                </div>


                                <div class="col-md-4 mb-2">

                                    <div class="bg-light rounded p-3">

                                        <small class="text-muted d-block">
                                            Mức độ
                                        </small>

                                        <strong class="fs-6">
                                            {{ $recommendationResult['level'] }}
                                        </strong>

                                    </div>

                                </div>


                                <div class="col-md-4 mb-2">

                                    <div class="bg-light rounded p-3">

                                        <small class="text-muted d-block">
                                            Điểm ưu tiên
                                        </small>

                                        <strong class="fs-5">
                                            {{ $recommendation['score'] }}
                                        </strong>

                                    </div>

                                </div>

                            </div>

                        @endif

                    </div>


                    <div class="col-lg-4 mt-4 mt-lg-0 text-lg-end">

                        @if (!empty($recommendation['content_id']))

                            <a
                                href="{{ route(
                                    'learning.show',
                                    $recommendation['content_id']
                                ) }}"
                                class="btn btn-primary"
                            >
                                Học nội dung này →
                            </a>

                        @endif

                    </div>

                </div>


                {{-- LÝ DO --}}

                @if (!empty($recommendation['reasons']))

                    <div class="mt-4 pt-4 border-top">

                        <h6 class="fw-bold mb-3">
                            Vì sao AI đề xuất nội dung này?
                        </h6>

                        <div class="row">

                            @foreach ($recommendation['reasons'] as $reason)

                                <div class="col-md-6 mb-2">

                                    <div class="d-flex align-items-start">

                                        <span class="text-success me-2">
                                            ✓
                                        </span>

                                        <span class="text-muted">
                                            {{ $reason }}
                                        </span>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </div>

    @else

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body text-center p-5">

                <div style="font-size: 50px;">
                    📚
                </div>

                <h5 class="fw-bold mt-3">
                    Chưa có đề xuất
                </h5>

                <p class="text-muted mb-0">
                    Hãy thêm nội dung học tập hoặc hoàn thành bài tập
                    để AI có dữ liệu phân tích.
                </p>

            </div>

        </div>

    @endif


    {{-- =========================================================
        4. DANH SÁCH NỘI DUNG ĐỀ XUẤT
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <h5 class="fw-bold mb-1">
                📋 Danh sách đề xuất
            </h5>

            <p class="text-muted mb-0">
                Các nội dung được xếp hạng dựa trên dữ liệu học tập của bạn.
            </p>

        </div>


        <div class="card-body px-4">

            @if (!empty($recommendations))

                @foreach ($recommendations as $item)

                    <div class="border rounded p-3 mb-3">

                        <div class="row align-items-center">

                            <div class="col-lg-7">

                                <div class="d-flex align-items-center mb-2">

                                    <h6 class="fw-bold mb-0">
                                        {{ $item['title'] }}
                                    </h6>


                                    @if (($item['priority'] ?? '') === 'Cao')

                                        <span class="badge bg-danger ms-2">
                                            Cao
                                        </span>

                                    @elseif (($item['priority'] ?? '') === 'Trung bình')

                                        <span class="badge bg-warning text-dark ms-2">
                                            Trung bình
                                        </span>

                                    @else

                                        <span class="badge bg-secondary ms-2">
                                            Thấp
                                        </span>

                                    @endif

                                </div>


                                <div class="small text-muted mb-2">

                                    {{ $item['subject'] ?? '' }}

                                    <span class="mx-1">
                                        •
                                    </span>

                                    {{ $item['difficulty'] ?? '' }}

                                </div>


                                @if (!empty($item['result']))

                                    <div class="small">

                                        Kết quả:

                                        <strong>
                                            {{ $item['result']['accuracy'] }}%
                                        </strong>

                                        <span class="text-muted">
                                            ({{ $item['result']['level'] }})
                                        </span>

                                    </div>

                                @else

                                    <div class="small text-muted">
                                        Chưa có kết quả bài tập.
                                    </div>

                                @endif

                            </div>


                            <div class="col-lg-3 mt-3 mt-lg-0">

                                <div class="small text-muted">
                                    Điểm ưu tiên
                                </div>

                                <strong>
                                    {{ $item['score'] ?? 0 }}
                                </strong>

                            </div>


                            <div class="col-lg-2 mt-3 mt-lg-0 text-lg-end">

                                @if (!empty($item['content_id']))

                                    <a
                                        href="{{ route(
                                            'learning.show',
                                            $item['content_id']
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Học ngay
                                    </a>

                                @endif

                            </div>

                        </div>


                        @if (!empty($item['reasons']))

                            <div class="mt-3 pt-3 border-top">

                                <small class="text-muted">

                                    <strong>
                                        Lý do:
                                    </strong>

                                    {{ implode(' ', $item['reasons']) }}

                                </small>

                            </div>

                        @endif

                    </div>

                @endforeach

            @else

                <div class="text-center py-4">

                    <p class="text-muted mb-0">
                        Chưa có nội dung để đề xuất.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        5. THỐNG KÊ LỘ TRÌNH
    ========================================================== --}}

    @if (!empty($learning_path_summary))

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <h5 class="fw-bold mb-1">
                    📊 Tình trạng lộ trình học tập
                </h5>

                <p class="text-muted mb-0">
                    Tổng hợp trạng thái học tập hiện tại.
                </p>

            </div>


            <div class="card-body px-4">

                <div class="row">

                    <div class="col-md mb-3">

                        <div class="bg-light rounded p-3 text-center">

                            <strong class="fs-4">
                                {{ $learning_path_summary['not_started'] ?? 0 }}
                            </strong>

                            <small class="d-block text-muted">
                                Chưa bắt đầu
                            </small>

                        </div>

                    </div>


                    <div class="col-md mb-3">

                        <div class="bg-light rounded p-3 text-center">

                            <strong class="fs-4">
                                {{ $learning_path_summary['in_progress'] ?? 0 }}
                            </strong>

                            <small class="d-block text-muted">
                                Đang học
                            </small>

                        </div>

                    </div>


                    <div class="col-md mb-3">

                        <div class="bg-light rounded p-3 text-center">

                            <strong class="fs-4">
                                {{ $learning_path_summary['needs_improvement'] ?? 0 }}
                            </strong>

                            <small class="d-block text-muted">
                                Cần cải thiện
                            </small>

                        </div>

                    </div>


                    <div class="col-md mb-3">

                        <div class="bg-light rounded p-3 text-center">

                            <strong class="fs-4">
                                {{ $learning_path_summary['needs_review'] ?? 0 }}
                            </strong>

                            <small class="d-block text-muted">
                                Cần ôn tập
                            </small>

                        </div>

                    </div>


                    <div class="col-md mb-3">

                        <div class="bg-light rounded p-3 text-center">

                            <strong class="fs-4">
                                {{ $learning_path_summary['completed'] ?? 0 }}
                            </strong>

                            <small class="d-block text-muted">
                                Đã hoàn thành
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- =========================================================
        6. NỘI DUNG TIẾP THEO
    ========================================================== --}}

    @if ($next_content)

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4">

                <h5 class="fw-bold">
                    🚀 Nội dung tiếp theo
                </h5>

                <p class="text-muted">
                    Bạn đã đạt kết quả tốt ở nội dung hiện tại.
                    Hệ thống đề xuất tiếp tục với:
                </p>

                <h5 class="fw-bold text-primary">
                    {{ $next_content['title'] }}
                </h5>

                <a
                    href="{{ route(
                        'learning.show',
                        $next_content['content_id']
                    ) }}"
                    class="btn btn-primary mt-2"
                >
                    Tiếp tục học
                </a>

            </div>

        </div>

    @endif

</div>

@endsection