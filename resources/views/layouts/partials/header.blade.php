<header class="store-main-header">
    <div class="container py-3">
        <div class="row align-items-center g-3">
            <div class="col-7 col-lg-3">
                <x-store.brand-logo />
            </div>

            <div class="col-5 d-flex justify-content-end gap-3 d-lg-none">
                @auth
                    <a class="store-header-action" href="{{ route('account.show') }}" aria-label="Tài khoản khách hàng">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <span class="d-none d-sm-inline">Tài khoản</span>
                    </a>
                @else
                    <a class="store-header-action" href="{{ route('login') }}" aria-label="Đăng nhập">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <span class="d-none d-sm-inline">Đăng nhập</span>
                    </a>
                @endauth
                <a
                    class="store-header-action store-header-cart"
                    href="{{ route('cart.index') }}"
                    aria-label="Giỏ hàng, {{ $cartItemCount }} sản phẩm"
                >
                    <i class="bi bi-bag" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline">Giỏ hàng</span>
                    @if ($cartItemCount > 0)
                        <span class="store-cart-count" aria-hidden="true">{{ $cartItemCount }}</span>
                    @endif
                </a>
            </div>

            <div class="col-12 col-lg-6">
                <form class="store-search input-group" action="{{ route('search') }}" method="GET" role="search">
                    <label class="visually-hidden" for="store-search">Tìm kiếm sản phẩm</label>
                    <input
                        class="form-control"
                        id="store-search"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Bạn đang tìm sản phẩm nào?"
                        autocomplete="off"
                    >
                    <button class="btn" type="submit" aria-label="Tìm kiếm">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </button>
                </form>
            </div>

            <div class="col-lg-3 d-none d-lg-flex justify-content-end gap-4">
                @auth
                    <a class="store-header-action" href="{{ route('account.show') }}" aria-label="Tài khoản khách hàng">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <span class="store-header-account-copy">
                            <span class="store-header-account-label">Tài khoản của tôi</span>
                            <strong>{{ auth()->user()->full_name }}</strong>
                        </span>
                    </a>
                @else
                    <a class="store-header-action" href="{{ route('login') }}" aria-label="Đăng nhập">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        <span>Đăng nhập</span>
                    </a>
                @endauth
                <a
                    class="store-header-action store-header-cart"
                    href="{{ route('cart.index') }}"
                    aria-label="Giỏ hàng, {{ $cartItemCount }} sản phẩm"
                >
                    <i class="bi bi-bag" aria-hidden="true"></i>
                    <span class="store-header-cart-copy">
                        <span class="store-header-account-label">Sẵn sàng mua sắm?</span>
                        <strong>Giỏ hàng</strong>
                    </span>
                    @if ($cartItemCount > 0)
                        <span class="store-cart-count" aria-hidden="true">{{ $cartItemCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</header>
