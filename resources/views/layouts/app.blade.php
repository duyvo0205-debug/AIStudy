<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'AIStudy')
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Google Font - Inter -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- AIStudy CSS -->
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >

    @stack('styles')
</head>

<body>

<div class="app-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <!-- LOGO -->
        <div class="logo-area">

            <div class="logo-icon">
                DAU
            </div>

            <div class="logo-text">
                <strong>AIStudy</strong>
                <span>HỖ TRỢ NHÂN HÓA HỌC TẬP</span>
            </div>

        </div>

        <!-- MENU -->
        <div class="menu-title">
            MENU
        </div>

        <nav class="sidebar-menu">

            <!-- DASHBOARD -->
            <a
                href="{{ route('dashboard') }}"
                class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                <span class="menu-icon">⌂</span>
                <span>Tổng quan</span>
            </a>

            <!-- MỤC TIÊU -->
            <a
                href="{{ route('learning-goals.index') }}"
                class="menu-item {{ request()->routeIs('learning-goals.*') ? 'active' : '' }}"
            >
                <span class="menu-icon">◎</span>
                <span>Mục tiêu</span>
            </a>

            <!-- LỊCH TỰ HỌC -->
            <a
                href="{{ route('study-schedules.index') }}"
                class="menu-item {{ request()->routeIs('study-schedules.*') ? 'active' : '' }}"
            >
                <span class="menu-icon">⏰</span>
                <span>Lịch tự học</span>
            </a>

            <!-- THỜI KHÓA BIỂU -->
            <a
                href="{{ route('school-schedules.index') }}"
                class="menu-item {{ request()->routeIs('school-schedules.*') ? 'active' : '' }}"
            >
                <span class="menu-icon">▦</span>
                <span>Thời khóa biểu</span>
            </a>

            <!-- HỌC TẬP -->
            <a
                href="{{ route('learning.index') }}"
                class="menu-item {{ request()->routeIs('learning.*') ? 'active' : '' }}"
            >
                <span class="menu-icon">▣</span>
                <span>Học tập</span>
            </a>

            <!-- BÀI TẬP -->
            <a
    href="{{ route('exercises.index') }}"
    class="menu-item {{ request()->routeIs('exercises.*') ? 'active' : '' }}"
>
    <span class="menu-icon">✓</span>
    <span>Bài tập</span>
</a>

            <!-- TIẾN ĐỘ -->
            <a
    href="{{ route('progress.index') }}"
    class="menu-item {{ request()->routeIs('progress.*') ? 'active' : '' }}"
>
    <span class="menu-icon">📊</span>
    <span>Tiến độ</span>
</a>

        </nav>

        <!-- AI LEARNING -->
        <div class="menu-title menu-title-space">
            AI LEARNING
        </div>

        <nav class="sidebar-menu">

            <!-- TRỢ LÝ AI -->
            <a
    href="{{ route('ai-assistant.index') }}"
    class="menu-item {{ request()->routeIs('ai-assistant.*') ? 'active' : '' }}"
>
    <span class="menu-icon">✦</span>
    <span>Trợ lý AI</span>

    <span class="menu-badge">
        AI
    </span>
</a>

            <!-- LỘ TRÌNH CÁ NHÂN -->
            <a
                href="{{ route('learning-path.index') }}"
                class="menu-item {{ request()->routeIs('learning-path.*') ? 'active' : '' }}"
            >
                <span class="menu-icon">◈</span>
                <span>Lộ trình cá nhân</span>
            </a>

            <!-- ĐỀ XUẤT AI -->
            <a
    href="{{ route('recommendations.index') }}"
    class="menu-item {{ request()->routeIs('recommendations.*') ? 'active' : '' }}"
>
    <span class="menu-icon">✧</span>
    <span>Đề xuất của AI</span>
</a>

        </nav>

        <!-- SIDEBAR BOTTOM -->
        <div class="sidebar-bottom">

            <!-- HỒ SƠ -->
            <a
                href="{{ route('profile') }}"
                class="sidebar-item"
            >
                <span class="sidebar-icon">👤</span>
                <span>Hồ sơ cá nhân</span>
            </a>

            <!-- CÀI ĐẶT -->
            <a
                href="#"
                class="sidebar-item"
            >
                <span class="sidebar-icon">⚙️</span>
                <span>Cài đặt</span>
            </a>

            <!-- USER -->
            @auth
                <div class="sidebar-user">

                    <a
                        href="{{ route('profile') }}"
                        class="sidebar-user-info"
                    >

                        <div class="sidebar-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="sidebar-user-text">
                            <strong>
                                {{ Auth::user()->name }}
                            </strong>

                            <small>
                                Người học
                            </small>
                        </div>

                    </a>

                    <!-- ĐĂNG XUẤT -->
                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                        class="logout-form"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="logout-button"
                            title="Đăng xuất"
                        >
                            ↪
                        </button>
                    </form>

                </div>
            @endauth

        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="top-header">

            <!-- HEADER LEFT -->
            <div class="header-left">

                <!-- MOBILE MENU -->
                <button
                    type="button"
                    class="mobile-menu"
                    id="mobileMenuButton"
                >
                    ☰
                </button>

                <!-- SEARCH -->
                <div class="search-box">

                    <span class="search-icon">
                        ⌕
                    </span>

                    <input
                        type="text"
                        id="globalSearch"
                        placeholder="Tìm kiếm bài học, tài liệu..."
                        autocomplete="off"
                    >

                    <span class="search-shortcut">
                        Ctrl K
                    </span>

                </div>

            </div>

            <!-- HEADER RIGHT -->
            <div class="header-right">

                <!-- THEME BUTTON -->
                <button
                    type="button"
                    class="header-button"
                    id="themeButton"
                    title="Đổi giao diện"
                >
                    ☀
                </button>

                <!-- NOTIFICATION -->
                <button
                    type="button"
                    class="header-button notification-button"
                    title="Thông báo"
                >
                    ♢

                    <span class="notification-dot"></span>
                </button>

                <!-- HEADER USER -->
                @auth
                    <a
                        href="{{ route('profile') }}"
                        class="header-user"
                        style="text-decoration: none; color: inherit;"
                    >

                        <div class="header-avatar">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        <div class="header-user-info">
                            <strong>
                                {{ Auth::user()->name }}
                            </strong>

                            <span>
                                Người học
                            </span>
                        </div>

                        <span class="header-arrow">
                            ▾
                        </span>

                    </a>
                @endauth

            </div>

        </header>

        <!-- PAGE CONTENT -->
        <section class="page-content">

            @yield('content')

        </section>

    </main>

</div>

<!-- Bootstrap JavaScript -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<!-- AIStudy JavaScript -->
@if(file_exists(public_path('js/app.js')))
    <script src="{{ asset('js/app.js') }}"></script>
@endif

@stack('scripts')

</body>

</html>