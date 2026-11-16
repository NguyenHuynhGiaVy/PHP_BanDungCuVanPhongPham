<div class="store-topbar">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
        <span class="store-topbar-message">
            <i class="bi bi-truck me-1" aria-hidden="true"></i>
            Văn phòng phẩm cho học tập và công việc mỗi ngày
        </span>
        <div class="store-topbar-links">
            <span class="store-topbar-note">
                <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                Thanh toán mô phỏng
            </span>
            <span class="store-topbar-note">
                <i class="bi bi-telephone me-1" aria-hidden="true"></i>
                Hotline: đang cập nhật
            </span>
            <a href="{{ route('products.index') }}">
                <i class="bi bi-box-seam me-1" aria-hidden="true"></i>
                Khám phá cửa hàng
            </a>
            <a href="{{ auth()->check() ? route('account.show') : route('login') }}">
                <i class="bi bi-chat-dots me-1" aria-hidden="true"></i>
                Hỗ trợ khách hàng
            </a>
        </div>
    </div>
</div>
