@extends('layouts.store')

@php
    $pageTitle = $category?->name
        ?? ($listingRoute === 'search' ? 'Tìm kiếm sản phẩm' : 'Tất cả sản phẩm');
@endphp

@section('title', $pageTitle.' | Stationery Store')

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
                <span class="store-section-kicker">Khám phá cửa hàng</span>
                <h1 class="store-section-title">{{ $pageTitle }}</h1>
                @if ($listingRoute === 'search' && filled($filters['q'] ?? null))
                    <p class="text-secondary mt-2 mb-0">
                        Kết quả cho “{{ $filters['q'] }}”
                    </p>
                @endif
            </div>
        </div>

        <div class="store-listing-toolbar">
            <p class="store-listing-count mb-0" aria-live="polite">
                {{ $products->total() }} sản phẩm
            </p>
            <button
                class="btn store-filter-toggle d-lg-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#product-filters"
                aria-controls="product-filters"
            >
                <i class="bi bi-sliders me-1" aria-hidden="true"></i>
                Bộ lọc và sắp xếp
            </button>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3" aria-label="Bộ lọc sản phẩm">
                <div
                    class="offcanvas-lg offcanvas-start store-filter-offcanvas"
                    id="product-filters"
                    tabindex="-1"
                    aria-labelledby="product-filters-title"
                >
                    <div class="offcanvas-header d-lg-none">
                        <h2 class="offcanvas-title h5 fw-bold" id="product-filters-title">Lọc sản phẩm</h2>
                        <button
                            class="btn-close"
                            type="button"
                            data-bs-dismiss="offcanvas"
                            data-bs-target="#product-filters"
                            aria-label="Đóng bộ lọc"
                        ></button>
                    </div>

                    <div class="offcanvas-body p-0">
                        <div class="store-filter-panel">
                            <div class="d-none d-lg-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 mb-0 fw-bold">Lọc sản phẩm</h2>
                                <i class="bi bi-sliders" aria-hidden="true"></i>
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

                            <form
                                action="{{ $category ? route('products.category', $category) : route($listingRoute) }}"
                                method="GET"
                                data-product-filter-form
                            >
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="filter-search">Tìm sản phẩm</label>
                                    <div class="input-group input-group-sm">
                                        <input
                                            class="form-control"
                                            id="filter-search"
                                            name="q"
                                            type="search"
                                            value="{{ $filters['q'] ?? '' }}"
                                            placeholder="Tên sản phẩm..."
                                        >
                                        <button class="btn btn-outline-primary" type="submit" aria-label="Tìm sản phẩm">
                                            <i class="bi bi-search" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="filter-brand">Thương hiệu</label>
                                    <select class="form-select form-select-sm" id="filter-brand" name="brand">
                                        <option value="">Tất cả thương hiệu</option>
                                        @foreach ($brands as $brand)
                                            <option
                                                value="{{ $brand->id }}"
                                                @selected((string) ($filters['brand'] ?? '') === (string) $brand->id)
                                            >
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <fieldset class="mb-3">
                                    <legend class="form-label small fw-semibold">Khoảng giá (₫)</legend>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="visually-hidden" for="filter-min-price">Giá từ</label>
                                            <input
                                                class="form-control form-control-sm"
                                                id="filter-min-price"
                                                name="min_price"
                                                type="number"
                                                min="0"
                                                step="1000"
                                                value="{{ $filters['min_price'] ?? '' }}"
                                                placeholder="Từ"
                                            >
                                        </div>
                                        <div class="col-6">
                                            <label class="visually-hidden" for="filter-max-price">Giá đến</label>
                                            <input
                                                class="form-control form-control-sm"
                                                id="filter-max-price"
                                                name="max_price"
                                                type="number"
                                                min="0"
                                                step="1000"
                                                value="{{ $filters['max_price'] ?? '' }}"
                                                placeholder="Đến"
                                            >
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="filter-sort">Sắp xếp</label>
                                    <select class="form-select form-select-sm" id="filter-sort" name="sort">
                                        <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>
                                            Mới nhất
                                        </option>
                                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>
                                            Giá tăng dần
                                        </option>
                                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>
                                            Giá giảm dần
                                        </option>
                                        <option value="name" @selected(($filters['sort'] ?? '') === 'name')>
                                            Tên A–Z
                                        </option>
                                    </select>
                                </div>

                                <button class="btn btn-primary btn-sm w-100" type="submit" data-filter-submit>
                                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                                    <span data-filter-submit-label>Áp dụng bộ lọc</span>
                                </button>
                                <span class="visually-hidden" role="status" aria-live="polite" data-filter-loading-status></span>
                            </form>

                            <a
                                class="store-filter-reset"
                                href="{{ $category ? route('products.category', $category) : route($listingRoute) }}"
                            >
                                Xóa bộ lọc
                            </a>

                            <hr class="my-4">

                            <h2 class="h6 mb-3 fw-bold">Danh mục</h2>
                            <ul class="store-filter-categories">
                                @foreach ($categories as $availableCategory)
                                    <li>
                                        <a
                                            @class(['active' => $category?->id === $availableCategory->id])
                                            href="{{ route('products.category', $availableCategory) }}"
                                        >
                                            {{ $availableCategory->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            <section class="col-12 col-lg-9" aria-label="Danh sách sản phẩm">
                @if ($products->isNotEmpty())
                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
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
                    <div class="store-empty-state">
                        <span class="store-empty-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <h2>Chưa tìm thấy sản phẩm phù hợp</h2>
                        <p class="mb-0">Thử đổi từ khóa hoặc điều chỉnh lại bộ lọc nhé.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
