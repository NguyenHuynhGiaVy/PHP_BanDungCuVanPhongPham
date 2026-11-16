@extends('layouts.store')

@section('title', 'Stationery Store | Văn phòng phẩm cho mọi ngày')

@section('content')
    <section class="store-hero">
        <div class="container">
            <div class="store-hero-panel">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="store-eyebrow">
                            <i class="bi bi-stars me-1" aria-hidden="true"></i>
                            Cảm hứng cho mỗi ngày
                        </span>
                        <h1 class="store-hero-title mt-3">
                            Không gian làm việc tiện nghi – Học tập hiệu quả
                        </h1>
                        <p class="store-hero-description mt-3 mb-4">
                            Tìm những món đồ thiết thực cho bàn học, góc làm việc và mọi ý tưởng đang chờ bạn bắt đầu.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a class="btn store-button-primary" href="{{ route('products.index') }}">
                                Mua sắm ngay
                                <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                            </a>
                            <a class="btn store-button-secondary" href="#danh-muc">
                                Khám phá danh mục
                            </a>
                        </div>
                        <ul class="store-hero-highlights" aria-label="Phù hợp cho nhiều nhu cầu">
                            <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Góc học tập</li>
                            <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Nơi làm việc</li>
                            <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Ý tưởng sáng tạo</li>
                        </ul>
                    </div>

                    <div class="col-lg-5">
                        <div class="store-hero-art" aria-hidden="true">
                            <span class="store-art-sun"></span>
                            <div class="store-art-note">
                                <i class="bi bi-journal-richtext"></i>
                                <span>Ghi chú<br>ý tưởng</span>
                            </div>
                            <div class="store-art-pencil">
                                <i class="bi bi-pencil-fill"></i>
                            </div>
                            <div class="store-art-ruler">
                                <i class="bi bi-rulers"></i>
                            </div>
                            <div class="store-art-paper">
                                <i class="bi bi-paperclip"></i>
                                <span></span><span></span><span></span>
                            </div>
                            <div class="store-art-caption">
                                <i class="bi bi-check-circle-fill"></i>
                                Bắt đầu từ góc nhỏ của bạn
                            </div>
                            <span class="store-art-dot store-art-dot-one"></span>
                            <span class="store-art-dot store-art-dot-two"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="store-benefits" aria-label="Điểm nổi bật">
        <div class="container">
            <div class="row row-cols-2 row-cols-lg-4 g-3">
                <div class="col">
                    <div class="store-benefit">
                        <span class="store-benefit-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <div>
                            <h2>Tìm kiếm dễ dàng</h2>
                            <p>Danh mục được sắp xếp rõ ràng</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="store-benefit">
                        <span class="store-benefit-icon"><i class="bi bi-pencil" aria-hidden="true"></i></span>
                        <div>
                            <h2>Cho mọi nhu cầu</h2>
                            <p>Từ bàn học đến văn phòng</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="store-benefit">
                        <span class="store-benefit-icon"><i class="bi bi-tags" aria-hidden="true"></i></span>
                        <div>
                            <h2>Thông tin minh bạch</h2>
                            <p>Giá và tồn kho từ dữ liệu cửa hàng</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="store-benefit">
                        <span class="store-benefit-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                        <div>
                            <h2>Góc nhỏ truyền cảm hứng</h2>
                            <p>Chọn món đồ hợp với bạn</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="store-section" id="danh-muc">
        <div class="container">
            <div class="store-section-heading">
                <div>
                    <span class="store-section-kicker">Khám phá cửa hàng</span>
                    <h2 class="store-section-title">Danh mục nổi bật</h2>
                </div>
                <a class="store-section-link" href="{{ route('products.index') }}">
                    Xem tất cả danh mục
                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>

            @if ($categories->isNotEmpty())
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                    @foreach ($categories as $category)
                        <div class="col">
                            @php
                                $categoryName = \Illuminate\Support\Str::lower($category->name);
                                $categoryIcon = match (true) {
                                    \Illuminate\Support\Str::contains($categoryName, ['bút']) => 'bi-pen',
                                    \Illuminate\Support\Str::contains($categoryName, ['bấm ghim', 'kẹp giấy', 'keo', 'băng']) => 'bi-paperclip',
                                    \Illuminate\Support\Str::contains($categoryName, ['sổ', 'vở']) => 'bi-journal-richtext',
                                    \Illuminate\Support\Str::contains($categoryName, ['giấy']) => 'bi-file-earmark-text',
                                    \Illuminate\Support\Str::contains($categoryName, ['bìa', 'hồ sơ', 'file']) => 'bi-folder2-open',
                                    \Illuminate\Support\Str::contains($categoryName, ['mỹ thuật', 'màu']) => 'bi-palette',
                                    \Illuminate\Support\Str::contains($categoryName, ['máy tính', 'thiết bị']) => 'bi-calculator',
                                    \Illuminate\Support\Str::contains($categoryName, ['ba lô', 'balo']) => 'bi-backpack',
                                    \Illuminate\Support\Str::contains($categoryName, ['bảng tên']) => 'bi-person-vcard',
                                    \Illuminate\Support\Str::contains($categoryName, ['dao rọc']) => 'bi-scissors',
                                    \Illuminate\Support\Str::contains($categoryName, ['hóa đơn']) => 'bi-receipt',
                                    \Illuminate\Support\Str::contains($categoryName, ['hộp dấu']) => 'bi-stamp',
                                    \Illuminate\Support\Str::contains($categoryName, ['kệ tài liệu']) => 'bi-inboxes',
                                    \Illuminate\Support\Str::contains($categoryName, ['dụng cụ']) => 'bi-tools',
                                    \Illuminate\Support\Str::contains($categoryName, ['thước']) => 'bi-rulers',
                                    default => 'bi-grid-1x2',
                                };
                            @endphp
                            <a
                                class="store-category-card"
                                href="{{ route('products.category', $category) }}"
                            >
                                <span class="store-category-icon" aria-hidden="true">
                                    <i class="bi {{ $categoryIcon }}"></i>
                                </span>
                                <span class="store-category-copy">
                                    <span class="store-category-name">{{ $category->name }}</span>
                                    <span class="store-category-action">Khám phá danh mục</span>
                                </span>
                                <span class="store-category-arrow" aria-hidden="true">
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="store-empty-state">
                    <i class="bi bi-inboxes" aria-hidden="true"></i>
                    <p class="mb-0">Danh mục sản phẩm đang được cập nhật.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="store-section store-section-tinted" id="ban-chay">
        <div class="container">
            <div class="store-section-heading">
                <div>
                    <span class="store-section-kicker">Được khách hàng lựa chọn</span>
                    <h2 class="store-section-title">Sản phẩm bán chạy</h2>
                </div>
                <a class="store-section-link" href="{{ route('products.index', ['sort' => 'newest']) }}">
                    Xem tất cả sản phẩm
                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>

            @if ($bestSellingProducts->isNotEmpty())
                <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($bestSellingProducts as $product)
                        <div class="col">
                            <x-store.product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="store-empty-state store-empty-state-compact">
                    <span class="store-empty-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                    <h3>Chưa có dữ liệu đơn hàng để xếp hạng</h3>
                    <p class="mb-0">Sản phẩm bán chạy sẽ hiển thị khi cửa hàng có đơn hàng đã thanh toán.</p>
                    <a class="store-section-link" href="{{ route('products.index') }}">Khám phá sản phẩm</a>
                </div>
            @endif
        </div>
    </section>

    <section class="store-section" id="san-pham">
        <div class="container">
            <div class="store-section-heading">
                <div>
                    <span class="store-section-kicker">Vừa được cập nhật</span>
                    <h2 class="store-section-title">Sản phẩm mới</h2>
                </div>
                <a class="store-section-link" href="{{ route('products.index', ['sort' => 'newest']) }}">
                    Xem tất cả sản phẩm
                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>

            @if ($newProducts->isNotEmpty())
                <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($newProducts as $product)
                        <div class="col">
                            <x-store.product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="store-empty-state">
                    <span class="store-empty-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                    <h3>Chúng tôi đang chuẩn bị sản phẩm</h3>
                    <p class="mb-0">Sản phẩm sẽ hiển thị tại đây sau khi được cập nhật vào cửa hàng.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="store-section store-section-tinted" id="uu-dai">
        <div class="container">
            <div class="store-section-heading">
                <div>
                    <span class="store-section-kicker">Giá ưu đãi từ cửa hàng</span>
                    <h2 class="store-section-title">Sản phẩm khuyến mãi</h2>
                </div>
                <a class="store-section-link" href="{{ route('products.index', ['sort' => 'price_asc']) }}">
                    Xem thêm sản phẩm
                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>

            @if ($promotionalProducts->isNotEmpty())
                <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($promotionalProducts as $product)
                        <div class="col">
                            <x-store.product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="store-empty-state store-empty-state-compact">
                    <span class="store-empty-icon"><i class="bi bi-tags" aria-hidden="true"></i></span>
                    <h3>Hiện chưa có giá khuyến mãi</h3>
                    <p class="mb-0">Khu vực này sẽ hiển thị sản phẩm khi cửa hàng cập nhật giá so sánh.</p>
                    <a class="store-section-link" href="{{ route('products.index') }}">Xem danh sách sản phẩm</a>
                </div>
            @endif
        </div>
    </section>

    @foreach ($productCategories as $category)
        <section class="store-section">
            <div class="container">
                <div class="store-section-heading">
                    <div>
                        <span class="store-section-kicker">Khám phá thêm</span>
                        <h2 class="store-section-title">{{ $category->name }}</h2>
                    </div>
                    <a class="store-section-link" href="{{ route('products.category', $category) }}">
                        Xem danh mục
                        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($category->products as $product)
                        <div class="col">
                            <x-store.product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <section class="store-promo">
        <div class="container">
            <div class="store-promo-panel">
                <div>
                    <span class="store-promo-kicker">
                        <i class="bi bi-lightbulb me-1" aria-hidden="true"></i>
                        Một góc nhỏ, nhiều cảm hứng
                    </span>
                    <h2 class="store-promo-title">Sắp xếp góc riêng, khơi nguồn ý tưởng mới.</h2>
                    <p class="mb-0">Khám phá những lựa chọn phù hợp với nhịp học tập và làm việc của bạn.</p>
                </div>
                <a class="btn store-promo-button" href="{{ route('products.index') }}">
                    Khám phá ngay
                    <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="store-section store-section-tinted">
        <div class="container">
            <div class="store-section-heading">
                <div>
                    <span class="store-section-kicker">Thương hiệu đồng hành</span>
                    <h2 class="store-section-title">Thương hiệu nổi bật</h2>
                </div>
                <a class="store-section-link" href="{{ route('products.index') }}">
                    Khám phá sản phẩm
                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>

            @if ($brands->isNotEmpty())
                <div class="row row-cols-2 row-cols-sm-4 row-cols-lg-5 g-3">
                    @foreach ($brands as $brand)
                        <div class="col">
                            <div class="store-brand-card">
                                @if ($brand->logo)
                                    <img
                                        class="store-brand-logo"
                                        src="{{ asset($brand->logo) }}"
                                        alt="{{ $brand->name }}"
                                        loading="lazy"
                                        data-brand-image
                                    >
                                    <span class="store-brand-name" data-brand-fallback hidden>
                                        {{ $brand->name }}
                                    </span>
                                @else
                                    <span class="store-brand-name">{{ $brand->name }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="store-empty-state">
                    <i class="bi bi-award" aria-hidden="true"></i>
                    <p class="mb-0">Thương hiệu đang được cập nhật.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
