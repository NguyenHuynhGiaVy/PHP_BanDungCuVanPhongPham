@extends('layouts.store')

@php
$pageTitle = $category?->name
?? ($listingRoute === 'search' ? 'Tìm kiếm sản phẩm' : 'Tất cả sản phẩm');
@endphp

@section('title', $pageTitle.' | Việt Thịnh')

@section('content')
<div class="container py-4 py-lg-5">
    <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            @if ($category)
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            @else
            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
            @endif
        </ol>
    </nav>

    <div class="store-listing-heading">
        <div>
            <h1 class="store-listing-title">{{ $pageTitle }}</h1>
            @if ($listingRoute === 'search' && filled($filters['q'] ?? null))
            <p class="text-secondary mt-1 mb-0 small">
                Kết quả cho “<strong>{{ $filters['q'] }}</strong>”
            </p>
            @endif
        </div>
    </div>

    <div class="store-listing-toolbar">
        <button
            class="btn store-filter-toggle d-lg-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#product-filters"
            aria-controls="product-filters">
            <i class="bi bi-sliders me-1" aria-hidden="true"></i>
            Bộ lọc
        </button>
    </div>

    <div class="row g-4 align-items-start">
        <!-- CỘT TRÁI: BỘ LỌC -->
        <aside class="col-12 col-lg-3" aria-label="Bộ lọc sản phẩm">
            <div
                class="offcanvas-lg offcanvas-start store-filter-offcanvas"
                id="product-filters"
                tabindex="-1"
                aria-labelledby="product-filters-title">
                <div class="offcanvas-header d-lg-none">
                    <h2 class="offcanvas-title h5 fw-bold" id="product-filters-title">Bộ Lọc Tìm Kiếm</h2>
                    <button
                        class="btn-close"
                        type="button"
                        data-bs-dismiss="offcanvas"
                        data-bs-target="#product-filters"
                        aria-label="Đóng bộ lọc"></button>
                </div>

                <div class="offcanvas-body p-0">
                    <div class="store-filter-sidebar">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h2 class="h5 mb-0 fw-bold text-dark"><i class="bi bi-filter-square text-primary me-1"></i> Bộ Lọc</h2>
                            <a href="{{ $category ? route('products.category', $category) : route($listingRoute) }}" class="text-muted small text-decoration-none hover-primary">Xóa tất cả</a>
                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger py-2" role="alert">
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ $category ? route('products.category', $category) : route($listingRoute) }}" method="GET" data-product-filter-form id="main-filter-form">
                            <!-- Tìm kiếm -->
                            <div class="store-filter-group">
                                <label class="form-label small fw-semibold" for="filter-search">Tìm sản phẩm</label>
                                <div class="input-group input-group-sm">
                                    <input class="form-control" id="filter-search" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="Tên sản phẩm...">
                                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </div>

                            <!-- 1. Sắp xếp -->
                            <div class="store-filter-group">
                                <div class="store-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-sort" role="button" aria-expanded="true">
                                    <span><i class="bi bi-arrow-down-up me-1 text-muted"></i> Sắp xếp</span> <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="collapse show" id="filter-sort">
                                    <div class="store-filter-body">
                                        <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()">
                                            <option value="newest" @selected(($filters['sort'] ?? 'newest' )==='newest' )>Mới nhất</option>
                                            <option value="price_asc" @selected(($filters['sort'] ?? '' )==='price_asc' )>Giá tăng dần</option>
                                            <option value="price_desc" @selected(($filters['sort'] ?? '' )==='price_desc' )>Giá giảm dần</option>
                                            <option value="name" @selected(($filters['sort'] ?? '' )==='name' )>Tên A–Z</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Nhãn hàng -->
                            <div class="store-filter-group">
                                <div class="store-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-brand" role="button" aria-expanded="true">
                                    <span><i class="bi bi-bookmark me-1 text-muted"></i> Nhãn hàng</span> <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="collapse show" id="filter-brand">
                                    <div class="store-filter-body">
                                        @foreach ($brands as $brand)
                                        <label class="store-filter-checkbox">
                                            <input type="radio" name="brand" value="{{ $brand->id }}" @checked((string) ($filters['brand'] ?? '' )===(string) $brand->id) onchange="this.form.submit()">
                                            <span class="checkbox-label">{{ $brand->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Lọc giá -->
                            <div class="store-filter-group border-0">
                                <div class="store-filter-title" data-bs-toggle="collapse" data-bs-target="#filter-price" role="button" aria-expanded="true">
                                    <span><i class="bi bi-tags me-1 text-muted"></i> Lọc giá</span> <i class="bi bi-chevron-down"></i>
                                </div>
                                <div class="collapse show" id="filter-price">
                                    <div class="store-filter-body">
                                        <!-- Hidden inputs for backend logic -->
                                        <input type="hidden" name="min_price" id="hidden-min-price" value="{{ $filters['min_price'] ?? '' }}">
                                        <input type="hidden" name="max_price" id="hidden-max-price" value="{{ $filters['max_price'] ?? '' }}">

                                        @php
                                        $min = $filters['min_price'] ?? null;
                                        $max = $filters['max_price'] ?? null;
                                        $ranges = [
                                        '0-50000' => 'Dưới 50.000₫',
                                        '50000-100000' => '50.000₫ - 100.000₫',
                                        '100000-200000' => '100.000₫ - 200.000₫',
                                        '200000-300000' => '200.000₫ - 300.000₫',
                                        '300000-' => 'Trên 300.000₫',
                                        ];
                                        @endphp

                                        @foreach($ranges as $val => $label)
                                        @php
                                        $parts = explode('-', $val);
                                        $rMin = $parts[0] === '' ? null : $parts[0];
                                        $rMax = $parts[1] === '' ? null : $parts[1];
                                        $isChecked = ((string)$min === (string)$rMin && (string)$max === (string)$rMax);
                                        @endphp
                                        <label class="store-filter-checkbox">
                                            <input type="radio" name="price_range" value="{{ $val }}" @checked($isChecked) onchange="updatePrice(this.value)">
                                            <span class="checkbox-label">{{ $label }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Nút Áp dụng cho Mobile -->
                            <div class="d-lg-none mt-3">
                                <button class="btn btn-primary w-100" type="submit">Áp dụng bộ lọc</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </aside>

        <!-- CỘT PHẢI: DANH SÁCH SẢN PHẨM -->
        <section class="col-12 col-lg-9" aria-label="Danh sách sản phẩm">
            @if ($products->isNotEmpty())
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-4 g-3">
                @foreach ($products as $product)
                <div class="col">
                    <x-store.product-card :product="$product" />
                </div>
                @endforeach
            </div>

            <div class="store-pagination mt-4">
                {{ $products->links() }}
            </div>
            @else
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 my-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle text-primary mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-search fs-1"></i>
                </div>
                <h2 class="h4 fw-bold text-dark mb-2">Chưa tìm thấy sản phẩm phù hợp</h2>
                <p class="text-muted small mb-4">Thử đổi từ khóa hoặc điều chỉnh lại bộ lọc nhé.</p>
            </div>
            @endif
        </section>
    </div>
</div>

<!-- JavaScript xử lý chọn giá -->
<script>
    function updatePrice(val) {
        const parts = val.split('-');
        document.getElementById('hidden-min-price').value = parts[0];
        document.getElementById('hidden-max-price').value = parts[1];
        document.querySelector('[data-product-filter-form]').submit();
    }
</script>
@endsection