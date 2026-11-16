<div class="store-navigation">
    <div class="container">
        <nav class="navbar navbar-expand-lg" aria-label="Điều hướng chính">
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#store-navigation"
                aria-controls="store-navigation"
                aria-expanded="false"
                aria-label="Mở hoặc đóng menu"
            >
                <i class="bi bi-list" aria-hidden="true"></i>
                <span class="ms-1">Danh mục</span>
            </button>

            <div class="collapse navbar-collapse" id="store-navigation">
                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1" aria-hidden="true"></i>
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <button
                            class="nav-link dropdown-toggle border-0 bg-transparent"
                            id="store-categories"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <i class="bi bi-grid me-1" aria-hidden="true"></i>
                            Danh mục sản phẩm
                        </button>
                        <ul class="dropdown-menu category-menu" aria-labelledby="store-categories">
                            <li class="store-mega-menu-heading">
                                <span>
                                    <strong>Khám phá danh mục</strong>
                                    <small>{{ $navigationCategories->count() }} danh mục từ cửa hàng</small>
                                </span>
                                <a href="{{ route('products.index') }}">
                                    Tất cả sản phẩm
                                    <i class="bi bi-arrow-up-right ms-1" aria-hidden="true"></i>
                                </a>
                            </li>
                            @forelse ($navigationCategories as $category)
                                <li>
                                    <a class="dropdown-item store-mega-menu-link" href="{{ route('products.category', $category) }}">
                                        <span class="store-mega-menu-icon" aria-hidden="true">
                                            <i class="bi bi-journal-text"></i>
                                        </span>
                                        <span>{{ $category->name }}</span>
                                        <i class="bi bi-chevron-right store-mega-menu-arrow" aria-hidden="true"></i>
                                    </a>
                                </li>
                            @empty
                                <li class="store-mega-menu-empty">
                                    <span class="dropdown-item-text text-secondary">
                                        Danh mục đang được cập nhật.
                                    </span>
                                </li>
                            @endforelse
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
