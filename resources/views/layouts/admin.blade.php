<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#12355b">
    <title>@yield('title', 'Quản trị | Stationery Store')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-layout">
    <div class="admin-shell">
        <aside class="admin-sidebar d-none d-lg-flex">
            @include('admin.partials.navigation')
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button
                    class="btn btn-outline-primary d-lg-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#admin-mobile-menu"
                    aria-controls="admin-mobile-menu"
                    aria-label="Mở menu quản trị"
                >
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
                <div class="admin-topbar-title">
                    <span class="admin-eyebrow">Stationery Store</span>
                    <strong>@yield('page-heading', 'Trung tâm quản trị')</strong>
                </div>
                <div class="admin-topbar-actions">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('home') }}">
                        <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>
                        Xem cửa hàng
                    </a>
                    <span class="admin-user-chip">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        {{ auth()->user()->full_name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-link btn-sm text-danger" type="submit">Đăng xuất</button>
                    </form>
                </div>
            </header>

            <main class="admin-content">
                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start admin-mobile-offcanvas" tabindex="-1" id="admin-mobile-menu" aria-labelledby="admin-mobile-menu-title">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title h5" id="admin-mobile-menu-title">Quản trị cửa hàng</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('admin.partials.navigation')
        </div>
    </div>

    <div class="modal fade" id="admin-confirm-modal" tabindex="-1" aria-labelledby="admin-confirm-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="admin-confirm-title">Xác nhận xóa</h2>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body" data-admin-confirm-message>Bạn có chắc muốn xóa mục này?</div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-danger" type="button" data-admin-confirm-submit>Xóa</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
