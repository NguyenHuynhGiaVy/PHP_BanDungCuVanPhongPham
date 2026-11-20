@extends('layouts.store')

@section('title', 'Việt Thịnh | Văn phòng phẩm & thiết bị văn phòng chính hãng')

@section('content')
<div class="container py-4">
    <!-- 1. HERO SECTION (VUÔNG VỨC) -->
    <section class="hero-grid">
        <div id="mainHeroCarousel" class="carousel slide hero-main carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="1"></button>
            </div>
            <div class="carousel-inner h-100 rounded-0 overflow-hidden shadow-sm">
                <div class="carousel-item active h-100">
                    <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 h-100 object-fit-cover" alt="Banner 1">
                </div>
                <div class="carousel-item h-100">
                    <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 h-100 object-fit-cover" alt="Banner 2">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainHeroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainHeroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <div class="hero-side">
            <a href="{{ route('products.index') }}" class="hero-side-banner rounded-0 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1587145820266-a5951ee6f620?auto=format&fit=crop&w=600&q=80" class="w-100 h-100 object-fit-cover" alt="Banner 1">
            </a>
            <a href="{{ route('about') }}" class="hero-side-banner rounded-0 overflow-hidden">
                <img src="https://images.unsplash.com/photo-1586075010923-2dd4570fb338?auto=format&fit=crop&w=600&q=80" class="w-100 h-100 object-fit-cover" alt="Banner 2">
            </a>
        </div>
    </section>

    <!-- 2. DANH MỤC SẢN PHẨM -->
    @if ($categories->isNotEmpty())
    <section class="home-section">
        <div class="home-section-header">
            <h2 class="home-section-title">Danh mục sản phẩm</h2>
        </div>
        <div class="bg-white border rounded-2 overflow-hidden shadow-sm">
            <div class="row row-cols-3 row-cols-sm-4 row-cols-md-6 row-cols-lg-8 g-0">
                @foreach ($categories as $category)
                @php
                $categoryName = \Illuminate\Support\Str::lower($category->name);
                $meta = match (true) {
                \Illuminate\Support\Str::contains($categoryName, ['bút']) => ['icon' => 'bi-pen-fill', 'bg' => '#f0f9ff', 'color' => '#0284c7'],
                \Illuminate\Support\Str::contains($categoryName, ['sổ', 'vở']) => ['icon' => 'bi-journal-richtext', 'bg' => '#fffbeb', 'color' => '#d97706'],
                \Illuminate\Support\Str::contains($categoryName, ['giấy']) => ['icon' => 'bi-file-earmark-text-fill', 'bg' => '#f0fdf4', 'color' => '#059669'],
                default => ['icon' => 'bi-grid-fill', 'bg' => '#f8fafc', 'color' => '#475569'],
                };
                @endphp
                <div class="col border-end border-bottom">
                    <a href="{{ route('products.category', $category) }}" class="d-flex flex-column align-items-center justify-content-center p-3 text-decoration-none text-center h-100 hover-bg-light transition">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px; background-color: {{ $meta['bg'] }}; color: {{ $meta['color'] }};">
                            <i class="bi {{ $meta['icon'] }} fs-3"></i>
                        </div>
                        <span class="text-dark mt-1 lh-sm" style="font-size: 0.8rem;">{{ $category->name }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 3. SẢN PHẨM BÁN CHẠY (GRID + ADS BÊN PHẢI) -->
    <section class="home-section" id="ban-chay">
        <div class="home-section-header">
            <h2 class="home-section-title text-danger"><i class="bi bi-fire"></i> Sản Phẩm Bán Chạy</h2>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="home-view-all">Xem tất cả <i class="bi bi-chevron-right"></i></a>
        </div>

        <div class="row g-3">
            {{-- Cột sản phẩm bên trái --}}
            <div class="col-12 col-lg-9">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3">
                    @if ($bestSellingProducts->isNotEmpty())
                    @foreach ($bestSellingProducts->take(8) as $product)
                    <div class="col"><x-store.product-card :product="$product" /></div>
                    @endforeach
                    @else
                    @foreach ($newProducts->take(8) as $product)
                    <div class="col"><x-store.product-card :product="$product" /></div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- Cột ảnh quảng cáo bên phải --}}
            <div class="col-lg-3 d-none d-lg-block">
                <a href="{{ route('products.index', ['sort' => 'price_asc']) }}" class="d-block h-100 rounded-3 overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=600&h=1200&q=80" class="w-100 h-100 object-fit-cover" alt="Quảng cáo Flash Sale" style="min-height: 400px;">
                </a>
            </div>
        </div>
    </section>

    <!-- 4. SẢN PHẨM MỚI VỀ -->
    @if ($newProducts->isNotEmpty())
    <section class="home-section" id="san-pham">
        <div class="home-section-header">
            <h2 class="home-section-title text-primary"><i class="bi bi-stars"></i> Sản Phẩm Mới Nhất</h2>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="home-view-all">Xem tất cả <i class="bi bi-chevron-right"></i></a>
        </div>

        <div class="row g-3 flex-row-reverse">
            {{-- Cột sản phẩm bên phải --}}
            <div class="col-12 col-lg-9">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2 g-md-3">
                    @foreach ($newProducts->take(8) as $product)
                    <div class="col"><x-store.product-card :product="$product" /></div>
                    @endforeach
                </div>
            </div>

            {{-- Cột ảnh quảng cáo bên trái --}}
            <div class="col-lg-3 d-none d-lg-block">
                <a href="{{ route('about') }}" class="d-block h-100 rounded-3 overflow-hidden shadow-sm">
                    <img src="https://images.unsplash.com/photo-1542435503-956c469947f6?auto=format&fit=crop&w=600&h=1200&q=80" class="w-100 h-100 object-fit-cover" alt="Quảng cáo Văn Phòng" style="min-height: 400px;">
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- 5. ẢNH QUẢNG CÁO -->
    <section class="mb-4 mt-2">
        <a href="{{ route('contact') }}" class="d-block w-100 rounded-0 overflow-hidden shadow-sm">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1600&q=80" class="w-100 object-fit-cover" alt="Giải pháp văn phòng doanh nghiệp" style="max-height: 250px;">
        </a>
    </section>

    <!-- 6. ĐỐI TÁC CHÍNH HÃNG -->
    @if ($brands->isNotEmpty())
    <section class="home-section text-center">
        <div class="home-section-header justify-content-center border-0 mb-3">
            <h2 class="home-section-title text-center" style="font-size: 1.1rem;">ĐỐI TÁC CHÍNH HÃNG</h2>
        </div>
        <div class="brand-marquee">
            <div class="brand-track">
                @for ($loopCount = 0; $loopCount < 2; $loopCount++)
                    @foreach ($brands as $brand)
                    <div class="brand-item">
                    @if ($brand->logo)
                    <img src="{{ str_starts_with($brand->logo, 'http') ? $brand->logo : asset($brand->logo) }}" alt="{{ $brand->name }}" style="max-height: 38px; max-width: 110px; object-fit: contain;">
                    @else
                    <span class="fw-bold fs-6 text-dark">{{ $brand->name }}</span>
                    @endif
            </div>
            @endforeach
            @endfor
        </div>
</div>
</section>
@endif
</div>
@endsection