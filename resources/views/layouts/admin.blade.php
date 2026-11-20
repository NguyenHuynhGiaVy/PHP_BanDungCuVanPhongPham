<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Quản trị | Việt Thịnh')</title>
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
                    class="btn btn-outline-dark d-lg-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#admin-mobile-menu"
                    aria-controls="admin-mobile-menu"
                    aria-label="Mở menu quản trị">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>

                {{-- Logo: chỉ hiện trên mobile (trên desktop logo nằm ở sidebar) --}}
                <a class="admin-topbar-logo d-lg-none" href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : route('home') }}" aria-label="Việt Thịnh - Quản trị">
                    <img src="{{ asset('images/logo.png') }}" alt="Việt Thịnh">
                </a>

                <div class="admin-topbar-actions">
                    <span class="admin-user-chip">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        {{ auth()->user()->full_name }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm admin-icon-button" type="submit" title="Đăng xuất" aria-label="Đăng xuất">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
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
            <h2 class="offcanvas-title h6 mb-0" id="admin-mobile-menu-title">Quản trị cửa hàng</h2>
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
    <div class="modal fade" id="coupon-send-modal" tabindex="-1" aria-labelledby="coupon-send-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">
                    <form id="coupon-send-form" method="POST">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <span class="text-secondary small d-block">Gửi mã khuyến mãi</span>
                                <h2 class="modal-title fs-5 fw-bold" id="coupon-send-title">Chọn khách hàng</h2>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="coupon-recipient-search">Tìm khách hàng</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input class="form-control" id="coupon-recipient-search" type="search" placeholder="Tên, email hoặc số điện thoại">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-secondary" data-coupon-recipient-count>0 khách hàng được chọn</span>
                                <button class="btn btn-link btn-sm text-decoration-none" type="button" data-coupon-select-all>Chọn tất cả</button>
                            </div>
                            <div class="border rounded-3 overflow-hidden" style="max-height: 320px; overflow-y: auto;">
                                @foreach ($customers ?? [] as $customer)
                                    <label class="d-flex align-items-center gap-3 px-3 py-3 border-bottom coupon-recipient-row" data-customer-search="{{ strtolower($customer->full_name.' '.$customer->email.' '.$customer->phone) }}">
                                        <input class="form-check-input mt-0" type="checkbox" name="recipient_ids[]" value="{{ $customer->id }}">
                                        <span class="rounded-circle bg-primary bg-opacity-10 text-primary d-grid place-items-center fw-bold" style="width: 38px; height: 38px;">{{ mb_substr($customer->full_name, 0, 1) }}</span>
                                        <span class="flex-grow-1">
                                            <strong class="d-block text-dark">{{ $customer->full_name }}</strong>
                                            <small class="text-secondary">{{ $customer->email }}</small>
                                        </span>
                                        <small class="text-secondary">{{ $customer->phone ?: 'Chưa có SĐT' }}</small>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary" data-coupon-send-submit disabled>
                                <i class="bi bi-send-fill me-1"></i>Gửi mã
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</body>

</html>