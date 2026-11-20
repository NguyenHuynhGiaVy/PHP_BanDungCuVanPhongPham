<div class="admin-sidebar-content">
    <a class="admin-brand" href="{{ route('admin.dashboard') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Việt Thịnh" class="admin-brand-logo">
    </a>
    <span class="admin-nav-label">TỔNG QUAN</span>
    <nav class="admin-nav" aria-label="Điều hướng quản trị">
        <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-1x2" aria-hidden="true"></i>Dashboard
        </a>
        <span class="admin-nav-label">DANH MỤC & SẢN PHẨM</span>
        <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
            <i class="bi bi-box-seam" aria-hidden="true"></i>Sản phẩm
        </a>
        <a class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <i class="bi bi-collection" aria-hidden="true"></i>Danh mục
        </a>
        <a class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
            <i class="bi bi-award" aria-hidden="true"></i>Thương hiệu
        </a>
        <a class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" href="{{ route('admin.suppliers.index') }}">
            <i class="bi bi-truck" aria-hidden="true"></i>Nhà cung cấp
        </a>
        <a class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
            <i class="bi bi-boxes" aria-hidden="true"></i>Tồn kho
        </a>
        <span class="admin-nav-label">ĐƠN HÀNG & KHÁCH HÀNG</span>
        <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
            <i class="bi bi-receipt" aria-hidden="true"></i>Đơn hàng
        </a>
        <a class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
            <i class="bi bi-people" aria-hidden="true"></i>Khách hàng
        </a>
        <a class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
            <i class="bi bi-ticket-perforated" aria-hidden="true"></i>Mã giảm giá
        </a>
        <a class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
            <i class="bi bi-star" aria-hidden="true"></i>Đánh giá
        </a>
        <span class="admin-nav-label">HỆ THỐNG & PHÂN QUYỀN</span>
        <a class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
            <i class="bi bi-person-badge" aria-hidden="true"></i>Quản lý nhân viên
        </a>
        <a class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
            <i class="bi bi-shield-lock" aria-hidden="true"></i>Phân quyền sử dụng
        </a>
    </nav>
</div>
