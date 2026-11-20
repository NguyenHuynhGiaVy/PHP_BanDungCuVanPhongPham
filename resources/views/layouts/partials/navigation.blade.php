@php
    $categoriesList = $navigationCategories ?? collect();

    // 1. Nhóm Văn phòng phẩm
    $vppCategories = $categoriesList->filter(function ($c) {
        $name = \Illuminate\Support\Str::lower($c->name);
        return \Illuminate\Support\Str::contains($name, [
            'bút viết', 'bút ký', 'bút bi', 'hồ sơ', 'bìa', 'kệ', 'văn phòng',
            'băng keo', 'keo', 'dấu', 'ghim', 'kẹp', 'máy tính', 'dao rọc', 'bảng tên', 'dụng cụ khác'
        ]);
    });

    // 2. Nhóm Dụng cụ học tập
    $hocTapCategories = $categoriesList->filter(function ($c) {
        $name = \Illuminate\Support\Str::lower($c->name);
        return \Illuminate\Support\Str::contains($name, [
            'học tập', 'vở', 'sổ', 'học sinh', 'thước', 'bút chì'
        ]);
    });

    // 3. Nhóm Mỹ thuật
    $myThuatCategories = $categoriesList->filter(function ($c) {
        $name = \Illuminate\Support\Str::lower($c->name);
        return \Illuminate\Support\Str::contains($name, ['mỹ thuật', 'bút lông']);
    });

    // 4. Nhóm Giấy in & Biểu mẫu
    $giayInCategories = $categoriesList->filter(function ($c) {
        $name = \Illuminate\Support\Str::lower($c->name);
        return \Illuminate\Support\Str::contains($name, ['giấy', 'hóa đơn']);
    });
@endphp

<div class="store-navigation">
    <div class="container">
        <nav class="navbar navbar-expand-lg py-0" aria-label="Điều hướng chính">
            <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#store-navigation" aria-controls="store-navigation" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="store-navigation">
                <ul class="navbar-nav align-items-lg-center w-100 justify-content-start gap-lg-2">
                    <!-- 1. VĂN PHÒNG PHẨM -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-custom-link" href="{{ route('products.index') }}" role="button">
                            VĂN PHÒNG PHẨM
                        </a>
                        <ul class="dropdown-menu store-nav-menu store-nav-menu-grid shadow-lg border-0">
                            @forelse ($vppCategories as $cat)
                                <li>
                                    <a class="dropdown-item" href="{{ route('products.category', $cat) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @empty
                                <li><a class="dropdown-item text-muted" href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                            @endforelse
                        </ul>
                    </li>

                    <!-- 2. DỤNG CỤ HỌC TẬP -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-custom-link" href="{{ route('products.index') }}" role="button">
                            DỤNG CỤ HỌC TẬP
                        </a>
                        <ul class="dropdown-menu store-nav-menu shadow-lg border-0">
                            @forelse ($hocTapCategories as $cat)
                                <li>
                                    <a class="dropdown-item" href="{{ route('products.category', $cat) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @empty
                                <li><a class="dropdown-item text-muted" href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                            @endforelse
                        </ul>
                    </li>

                    <!-- 3. MỸ THUẬT -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-custom-link" href="{{ route('products.index') }}" role="button">
                            MỸ THUẬT
                        </a>
                        <ul class="dropdown-menu store-nav-menu shadow-lg border-0">
                            @forelse ($myThuatCategories as $cat)
                                <li>
                                    <a class="dropdown-item" href="{{ route('products.category', $cat) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @empty
                                <li><a class="dropdown-item text-muted" href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                            @endforelse
                        </ul>
                    </li>

                    <!-- 4. GIẤY IN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle nav-custom-link" href="{{ route('products.index') }}" role="button">
                            GIẤY IN
                        </a>
                        <ul class="dropdown-menu store-nav-menu shadow-lg border-0">
                            @forelse ($giayInCategories as $cat)
                                <li>
                                    <a class="dropdown-item" href="{{ route('products.category', $cat) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @empty
                                <li><a class="dropdown-item text-muted" href="{{ route('products.index') }}">Tất cả sản phẩm</a></li>
                            @endforelse
                        </ul>
                    </li>

                    <!-- 5. TẤT CẢ SẢN PHẨM -->
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link" href="{{ route('products.index') }}">SẢN PHẨM</a>
                    </li>

                    <!-- 6. GIỚI THIỆU -->
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link" href="{{ route('about') }}">GIỚI THIỆU</a>
                    </li>

                    <!-- 7. HỖ TRỢ & LIÊN HỆ (LINK TRỰC TIẾP) -->
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link" href="{{ route('contact') }}">HỖ TRỢ & LIÊN HỆ</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
