<div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
    <!-- User summary header -->
    <div class="p-3 bg-light border-bottom d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem; flex-shrink: 0;">
            {{ mb_substr(auth()->user()->full_name ?? 'U', 0, 1) }}
        </div>
        <div class="min-w-0">
            <h6 class="mb-0 fw-bold text-dark text-truncate">{{ auth()->user()->full_name }}</h6>
            <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">{{ auth()->user()->email }}</small>
        </div>
    </div>

    <!-- Navigation links -->
    <nav class="p-2 d-flex flex-column gap-1" aria-label="Điều hướng tài khoản">
        <a
            class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none small fw-semibold transition {{ request()->routeIs('account.show') ? 'bg-primary text-white' : 'text-dark hover-bg-light' }}"
            href="{{ route('account.show') }}"
        >
            <i class="bi bi-person fs-5"></i>
            <span>Thông tin cá nhân</span>
        </a>

        <a
            class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none small fw-semibold transition {{ request()->routeIs('orders.*') ? 'bg-primary text-white' : 'text-dark hover-bg-light' }}"
            href="{{ route('orders.index') }}"
        >
            <i class="bi bi-receipt fs-5"></i>
            <span>Lịch sử đơn hàng</span>
        </a>

        <a
            class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none small fw-semibold transition {{ request()->routeIs('wishlist.*') ? 'bg-primary text-white' : 'text-dark hover-bg-light' }}"
            href="{{ route('wishlist.index') }}"
        >
            <i class="bi bi-heart fs-5"></i>
            <span>Sản phẩm yêu thích</span>
        </a>

        <a
            class="d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none small fw-semibold transition text-dark hover-bg-light"
            href="{{ route('cart.index') }}"
        >
            <i class="bi bi-cart fs-5"></i>
            <span>Giỏ hàng của tôi</span>
        </a>

        <hr class="my-1">

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="w-100 d-flex align-items-center gap-2 px-3 py-2 rounded-2 text-decoration-none small fw-semibold text-danger border-0 bg-transparent hover-bg-light transition text-start" type="submit">
                <i class="bi bi-box-arrow-right fs-5"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </nav>
</div>
