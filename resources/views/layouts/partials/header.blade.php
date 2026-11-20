<header class="store-main-header py-3">
    <div class="container">
        <div class="row align-items-center g-3">
            <!-- Logo (Trái) - Rộng rãi để logo to đẹp -->
            <div class="col-lg-3 col-6">
                <x-store.brand-logo />
            </div>

            <!-- Search (Nằm ngay cạnh Logo, kéo dài) -->
            <div class="col-lg-5 d-none d-lg-block">
                <form class="store-search d-flex" action="{{ route('search') }}" method="GET" role="search">
                    <input
                        class="form-control border-0"
                        id="store-search"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Tìm kiếm sản phẩm..."
                        autocomplete="off">
                    <button class="btn px-4" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- 3 tiêu chí nằm ngay sát dưới thanh tìm kiếm -->
                <div class="d-flex align-items-center gap-3 mt-1 store-search-sub-badges">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-award"></i>
                        <span>Sản phẩm chính hãng</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-truck"></i>
                        <span>Trợ phí vận chuyển</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-check2-circle"></i>
                        <span>Hỗ trợ xuất VAT</span>
                    </div>
                </div>
            </div>

            <!-- Actions (Phải) -->
            <div class="col-lg-4 col-6 d-flex justify-content-end align-items-center gap-3 gap-xl-4">
                <!-- Hotline -->
                <div class="d-none d-xl-flex align-items-center gap-2 text-dark">
                    <i class="bi bi-telephone fs-4 text-primary"></i>
                    <div class="d-flex flex-column lh-1">
                        <small style="font-size: 0.7rem; color: #64748b;">Hotline</small>
                        <strong style="font-size: 0.9rem; color: #1e293b;">1900 1288</strong>
                    </div>
                </div>

                <!-- Account -->
                @auth
                <a class="store-header-action" href="{{ auth()->user()->isStaffOrAdmin() ? route('admin.dashboard') : route('account.show') }}">
                    <i class="bi bi-person fs-4"></i>
                    <span class="d-none d-xl-block">{{ auth()->user()->isStaffOrAdmin() ? 'Quản trị' : 'Tài khoản' }}</span>
                </a>
                @if (! auth()->user()->isStaffOrAdmin())
                @php
                    $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
                @endphp
                <a class="store-header-action position-relative" href="{{ route('account.show') }}" title="Thông báo" aria-label="Thông báo">
                    <i class="bi bi-bell fs-4"></i>
                    @if ($unreadNotificationCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">{{ $unreadNotificationCount }}</span>
                    @endif
                </a>
                @endif
                @else
                <a class="store-header-action" href="{{ route('login') }}">
                    <i class="bi bi-person fs-4"></i>
                    <span class="d-none d-xl-block">Đăng nhập</span>
                </a>
                @endauth

                <!-- Cart -->
                <a class="store-header-action" href="{{ route('cart.index') }}">
                    <span class="position-relative d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-cart2 fs-3"></i>
                        @if ($cartItemCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; min-width: 18px; height: 18px; padding: 0 4px; display: inline-flex; align-items: center; justify-content: center; transform: translate(-10%, -20%) !important;">
                            {{ $cartItemCount }}
                        </span>
                        @endif
                    </span>
                    <span class="d-none d-xl-block ms-1">Giỏ hàng</span>
                </a>
            </div>

            <!-- Search Mobile -->
            <div class="col-12 d-lg-none mt-2">
                <form class="store-search d-flex" action="{{ route('search') }}" method="GET" role="search">
                    <input class="form-control border-0" type="search" name="q" placeholder="Tìm kiếm sản phẩm..." required>
                    <button class="btn px-3" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</header>
