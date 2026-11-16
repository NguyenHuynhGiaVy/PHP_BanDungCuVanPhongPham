<nav class="store-account-nav" aria-label="Điều hướng tài khoản">
    <a
        class="{{ request()->routeIs('account.show') && ! request()->query('section') ? 'active' : '' }}"
        href="{{ route('account.show') }}"
    >
        <i class="bi bi-person me-2" aria-hidden="true"></i>Thông tin cá nhân
    </a>
    <a
        class="{{ request()->routeIs('account.show') && request()->query('section') === 'addresses' ? 'active' : '' }}"
        href="{{ route('account.show', ['section' => 'addresses']).'#address-book' }}"
    >
        <i class="bi bi-geo-alt me-2" aria-hidden="true"></i>Sổ địa chỉ
    </a>
    <a class="{{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
        <i class="bi bi-receipt me-2" aria-hidden="true"></i>Lịch sử đơn hàng
    </a>
    <a class="{{ request()->routeIs('wishlist.*') ? 'active' : '' }}" href="{{ route('wishlist.index') }}">
        <i class="bi bi-heart me-2" aria-hidden="true"></i>Sản phẩm yêu thích
    </a>
    <a href="{{ route('cart.index') }}">
        <i class="bi bi-bag me-2" aria-hidden="true"></i>Giỏ hàng
    </a>
</nav>
