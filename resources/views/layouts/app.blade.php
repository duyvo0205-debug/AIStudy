
<!DOCTYPE html>
<html lang="vi" id="appHtml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'AIStudy')</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Google Font - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

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
<a href="{{ route('dashboard') }}" class="logo-area">

    <div class="logo-icon">
        DAU
    </div>

    <div class="logo-text">
        <strong>AIStudy</strong>
        <span>HỖ TRỢ NHÂN HÓA HỌC TẬP</span>
    </div>

</a>

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
                <span class="menu-badge">AI</span>
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
            <a href="{{ route('profile') }}" class="sidebar-item">
                <span class="sidebar-icon">👤</span>
                <span>Hồ sơ cá nhân</span>
            </a>

            <!-- CÀI ĐẶT -->
            <a href="#" class="sidebar-item">
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
                            <strong>{{ Auth::user()->name }}</strong>
                            <small>Người học</small>
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

                    <span class="search-icon">⌕</span>

                    <input
    type="text"
    id="globalSearch"
    data-search-url="{{ route('search.index') }}"
    placeholder="Tìm kiếm bài học, tài liệu..."
    autocomplete="off"
    aria-label="Tìm kiếm toàn website"
>

                    <span class="search-shortcut">
                        Ctrl K
                    </span>

                </div>

            </div>

            <!-- HEADER RIGHT -->
            <div class="header-right">

                <!-- DARK MODE -->
                <button
                    type="button"
                    class="header-button"
                    id="themeButton"
                    title="Chuyển sang giao diện tối"
                    aria-label="Chuyển đổi giao diện"
                >
                    <span id="themeIcon">☾</span>
                </button>

                <!-- NOTIFICATION -->
@auth
    @php
        $unreadNotificationCount = Auth::user()
            ->unreadNotifications()
            ->count();

        $headerNotifications = Auth::user()
            ->notifications()
            ->latest()
            ->take(5)
            ->get();
    @endphp

    <div class="dropdown notification-dropdown">

        <button
            type="button"
            class="header-button notification-button"
            id="notificationDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="Thông báo"
            aria-label="Mở thông báo"
        >
            ♢

            @if($unreadNotificationCount > 0)
                <span class="notification-dot"></span>
            @endif
        </button>

        <div
            class="dropdown-menu dropdown-menu-end notification-menu"
            aria-labelledby="notificationDropdown"
            style="width: 350px; max-width: calc(100vw - 30px); padding: 0; overflow: hidden;"
        >

            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">

                <div>
                    <strong>Thông báo</strong>

                    @if($unreadNotificationCount > 0)
                        <span class="badge bg-danger ms-1">
                            {{ $unreadNotificationCount }}
                        </span>
                    @endif
                </div>

                <a
                    href="{{ route('notifications.index') }}"
                    class="text-decoration-none small"
                >
                    Xem tất cả
                </a>

            </div>

            <!-- NOTIFICATION LIST -->
            <div style="max-height: 350px; overflow-y: auto;">

                @forelse($headerNotifications as $notification)

                    <a
                        href="{{ route('notifications.index') }}"
                        class="dropdown-item text-wrap p-3 border-bottom"
                        style="
                            white-space: normal;
                            background: {{ $notification->read_at ? 'transparent' : '#f4f2ff' }};
                        "
                    >

                        <div class="d-flex align-items-start gap-2">

                            <div
                                class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                style="
                                    width: 36px;
                                    height: 36px;
                                    background: #eeeaff;
                                    color: #6d5dfc;
                                "
                            >
                                ♢
                            </div>

                            <div class="flex-grow-1">

                                <div class="d-flex justify-content-between align-items-start gap-2">

                                    <strong style="font-size: 13px;">
                                        {{ $notification->data['title'] ?? 'Thông báo mới' }}
                                    </strong>

                                    @if(!$notification->read_at)
                                        <span
                                            class="rounded-circle flex-shrink-0"
                                            style="
                                                width: 8px;
                                                height: 8px;
                                                background: #6d5dfc;
                                                margin-top: 5px;
                                            "
                                        ></span>
                                    @endif

                                </div>

                                <p
                                    class="mb-1 text-muted"
                                    style="font-size: 12px;"
                                >
                                    {{ $notification->data['message'] ?? '' }}
                                </p>

                                <small class="text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>

                            </div>

                        </div>

                    </a>

                @empty

                    <div class="text-center p-4">

                        <div style="font-size: 28px; color: #a0a0a0;">
                            ♢
                        </div>

                        <p class="mb-0 text-muted small">
                            Bạn chưa có thông báo nào.
                        </p>

                    </div>

                @endforelse

            </div>

            <!-- FOOTER -->
            <a
                href="{{ route('notifications.index') }}"
                class="d-block text-center p-3 text-decoration-none border-top small"
            >
                Xem tất cả thông báo
            </a>

        </div>

    </div>
@endauth


<!-- HEADER USER -->
@auth
    <div class="dropdown header-user-dropdown">

        <button
            type="button"
            class="header-user border-0 bg-transparent"
            id="headerUserDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="cursor: pointer;"
        >
            <div class="header-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            <div class="header-user-info">
                <strong>{{ Auth::user()->name }}</strong>
                <span>Người học</span>
            </div>

            <span class="header-arrow">▾</span>
        </button>

        <div
            class="dropdown-menu dropdown-menu-end shadow-sm"
            aria-labelledby="headerUserDropdown"
            style="min-width: 210px; padding: 8px;"
        >

            <!-- HỒ SƠ CÁ NHÂN -->
            <a
                href="{{ route('profile') }}"
                class="dropdown-item rounded-2 py-2"
            >
                <span class="me-2">👤</span>
                Hồ sơ cá nhân
            </a>

            <div class="dropdown-divider"></div>

            <!-- ĐĂNG XUẤT -->
            <form
                action="{{ route('logout') }}"
                method="POST"
            >
                @csrf

                <button
                    type="submit"
                    class="dropdown-item rounded-2 py-2 text-danger"
                >
                    <span class="me-2">↪</span>
                    Đăng xuất
                </button>
            </form>

        </div>

    </div>
@endauth


        </header>

        <!-- PAGE CONTENT -->
        <section class="page-content">
            @yield('content')
        </section>

    </main>

</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- AIStudy JavaScript -->
@if(file_exists(public_path('js/app.js')))
    <script src="{{ asset('js/app.js') }}"></script>
@endif
<!-- GLOBAL SEARCH -->
<script src="{{ asset('js/global-search.js') }}"></script>

<!-- PAGE SCRIPTS -->
@stack('scripts')

<!-- DARK MODE -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const html = document.getElementById('appHtml');
    const themeButton = document.getElementById('themeButton');
    const themeIcon = document.getElementById('themeIcon');

    if (!html || !themeButton || !themeIcon) {
        return;
    }

    // Áp dụng giao diện
    function applyTheme(theme) {

        html.setAttribute('data-theme', theme);

        if (theme === 'dark') {

            themeIcon.textContent = '☀';
            themeButton.title = 'Chuyển sang giao diện sáng';
            themeButton.setAttribute(
                'aria-label',
                'Chuyển sang giao diện sáng'
            );

        } else {

            themeIcon.textContent = '☾';
            themeButton.title = 'Chuyển sang giao diện tối';
            themeButton.setAttribute(
                'aria-label',
                'Chuyển sang giao diện tối'
            );

        }

    }

    // Đọc giao diện đã lưu
    let savedTheme = 'light';

    try {
        savedTheme = localStorage.getItem('aistudy-theme') || 'light';
    } catch (error) {
        console.warn('Không thể đọc giao diện:', error);
    }

    // Khởi tạo giao diện
    applyTheme(savedTheme);

    // Chuyển đổi giao diện khi nhấn nút
    themeButton.addEventListener('click', function () {

        const currentTheme = html.getAttribute('data-theme');

        const newTheme = currentTheme === 'dark'
            ? 'light'
            : 'dark';

        applyTheme(newTheme);

        // Lưu lựa chọn
        try {
            localStorage.setItem('aistudy-theme', newTheme);
        } catch (error) {
            console.warn('Không thể lưu giao diện:', error);
        }

    });

});
</script>

</body>
</html>
