@extends('layouts.store')

@section('title', $product->name.' | Stationery Store')

@section('content')
    @php
        $selectedVariant = $product->variants->firstWhere('id', (int) old('variant_id'))
            ?? $product->variants->first();
        $selectedInventory = $selectedVariant?->inventory;
        $selectedQuantity = $selectedInventory
            ? max(0, $selectedInventory->quantity - $selectedInventory->reserved_quantity)
            : null;
        $primaryImage = $product->images->first();
    @endphp

    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('products.category', $product->category) }}">{{ $product->category->name }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <article class="store-product-detail">
            <div class="row g-4 g-lg-5">
                <div class="col-12 col-lg-6">
                    <div class="store-detail-gallery" data-product-gallery>
                        <div class="store-detail-image">
                            @if ($primaryImage)
                                <img
                                    src="{{ asset($primaryImage->image_url) }}"
                                    alt="{{ $product->name }}"
                                    data-gallery-main
                                    data-product-image
                                >
                                <span class="store-product-image-fallback" data-product-image-fallback hidden>
                                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                                </span>
                            @else
                                <span class="store-product-image-fallback">
                                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                                </span>
                            @endif
                        </div>

                        @if ($product->images->count() > 1)
                            <div class="store-detail-thumbnails" aria-label="Ảnh sản phẩm">
                                @foreach ($product->images as $image)
                                    <button
                                        class="store-detail-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        type="button"
                                        data-gallery-thumbnail
                                        data-image-src="{{ asset($image->image_url) }}"
                                        data-image-alt="{{ $product->name }} - ảnh {{ $loop->iteration }}"
                                        aria-label="Xem ảnh sản phẩm {{ $loop->iteration }}"
                                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        <img
                                            src="{{ asset($image->image_url) }}"
                                            alt=""
                                            loading="lazy"
                                            data-product-image
                                        >
                                        <span class="store-product-image-fallback" hidden>
                                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <p class="store-detail-brand mb-2">
                        {{ $product->brand?->name ?? 'Đang cập nhật thương hiệu' }}
                    </p>
                    <h1 class="store-detail-title">{{ $product->name }}</h1>
                    <p class="store-detail-sku">
                        Mã sản phẩm:
                        <span data-variant-sku>{{ $selectedVariant?->sku ?? 'Đang cập nhật' }}</span>
                    </p>

                    <div class="store-detail-price">
                        @if ($selectedVariant)
                            <span data-variant-price>{{ number_format((float) $selectedVariant->price, 0, ',', '.') }} ₫</span>
                            <del
                                data-variant-compare
                                @if (!$selectedVariant->compare_price || $selectedVariant->compare_price <= $selectedVariant->price)
                                    hidden
                                @endif
                            >
                                {{ $selectedVariant->compare_price
                                    ? number_format((float) $selectedVariant->compare_price, 0, ',', '.').' ₫'
                                    : '' }}
                            </del>
                        @else
                            <span class="store-product-price-pending" data-variant-price>Giá đang cập nhật</span>
                            <del data-variant-compare hidden></del>
                        @endif
                    </div>

                    <form action="{{ route('cart.items.store') }}" method="POST" data-product-cart-form>
                        @csrf
                        @if ($product->variants->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="product-variant">Phân loại</label>
                                <select
                                    class="form-select @error('variant_id') is-invalid @enderror"
                                    id="product-variant"
                                    name="variant_id"
                                    required
                                    @error('variant_id') aria-describedby="product-variant-error" @enderror
                                    @error('variant_id') aria-invalid="true" @enderror
                                    data-variant-selector
                                >
                                    @foreach ($product->variants as $variant)
                                        @php
                                            $inventory = $variant->inventory;
                                            $availableQuantity = $inventory
                                                ? max(0, $inventory->quantity - $inventory->reserved_quantity)
                                                : null;
                                        @endphp
                                        <option
                                            value="{{ $variant->id }}"
                                            data-price="{{ $variant->price }}"
                                            data-compare-price="{{ $variant->compare_price }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-available="{{ $availableQuantity ?? '' }}"
                                            @selected($selectedVariant?->id === $variant->id)
                                        >
                                            {{ $variant->variant_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('variant_id')
                                    <div class="invalid-feedback d-block" id="product-variant-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <p class="store-detail-notice">
                                Giá và phân loại sản phẩm đang được cập nhật.
                            </p>
                        @endif

                    <p class="store-detail-stock" data-variant-stock aria-live="polite">
                        @if ($selectedQuantity === null)
                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                            Đang cập nhật tồn kho
                        @elseif ($selectedQuantity > 0)
                            <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                            Còn hàng ({{ $selectedQuantity }})
                        @else
                            <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                            Tạm hết hàng
                        @endif
                    </p>

                    <div class="store-detail-purchase">
                        <div>
                            <label class="form-label small fw-semibold" for="product-quantity">Số lượng</label>
                            <input
                                class="form-control @error('quantity') is-invalid @enderror"
                                id="product-quantity"
                                type="number"
                                value="{{ old('quantity', 1) }}"
                                min="1"
                                name="quantity"
                                @error('quantity') aria-describedby="product-quantity-error" @enderror
                                @error('quantity') aria-invalid="true" @enderror
                                @if ($selectedQuantity !== null)
                                    max="{{ max(1, $selectedQuantity) }}"
                                @endif
                                data-variant-quantity
                            >
                            @error('quantity')
                                <span class="invalid-feedback d-block" id="product-quantity-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <button
                            class="btn store-button-primary"
                            type="submit"
                            name="purchase_intent"
                            value="cart"
                            @disabled($selectedQuantity === null || $selectedQuantity < 1 || $product->variants->isEmpty())
                            data-variant-submit
                            data-pending-label="Đang thêm..."
                        >
                            <i class="bi bi-bag-plus me-1" aria-hidden="true"></i>
                            <span data-cart-submit-label>Thêm vào giỏ</span>
                        </button>
                        <button
                            class="btn btn-outline-primary"
                            type="submit"
                            name="purchase_intent"
                            value="checkout"
                            @disabled($selectedQuantity === null || $selectedQuantity < 1 || $product->variants->isEmpty())
                            data-variant-submit
                            data-pending-label="Đang chuyển..."
                        >
                            <i class="bi bi-lightning-charge me-1" aria-hidden="true"></i>
                            <span data-cart-submit-label>Mua ngay</span>
                        </button>
                    </div>
                    </form>
                    <p class="store-detail-notice mt-2">
                        Thanh toán tại đây chỉ mô phỏng cho mục đích học tập, không phát sinh giao dịch thật.
                    </p>
                    <form action="{{ route('wishlist.store', $product) }}" method="POST" class="mb-3">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                            <i class="bi bi-heart me-1" aria-hidden="true"></i>
                            Lưu vào yêu thích
                        </button>
                    </form>

                    <div class="store-detail-meta">
                        <p>
                            <span>Danh mục</span>
                            <a href="{{ route('products.category', $product->category) }}">
                                {{ $product->category->name }}
                            </a>
                        </p>
                        <p>
                            <span>Thương hiệu</span>
                            <span>{{ $product->brand?->name ?? 'Đang cập nhật' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            @if ($product->description)
                <section class="store-detail-description mt-5">
                    <h2 class="store-section-title h4 mb-3">Mô tả sản phẩm</h2>
                    <div class="store-description-content">{{ $product->description }}</div>
                </section>
            @endif

            <section class="store-detail-reviews mt-5">
                <div class="store-section-heading">
                    <div>
                        <span class="store-section-kicker">Chia sẻ từ khách hàng</span>
                        <h2 class="store-section-title h4">Đánh giá sản phẩm</h2>
                    </div>
                    @if ($product->approved_reviews_count > 0)
                        <span class="store-review-summary">
                            <i class="bi bi-star-fill me-1" aria-hidden="true"></i>
                            {{ number_format((float) $product->approved_reviews_average, 1, ',', '.') }}
                            / 5 · {{ $product->approved_reviews_count }} đánh giá
                        </span>
                    @endif
                </div>

                @if ($product->reviews->isNotEmpty())
                    <div class="store-review-list">
                        @foreach ($product->reviews as $review)
                            <article class="store-review">
                                <div class="store-review-heading">
                                    <strong>{{ $review->user->full_name }}</strong>
                                    <span class="store-review-rating" aria-label="{{ $review->rating }} trên 5 sao">
                                        @for ($rating = 0; $rating < $review->rating; $rating++)
                                            <i class="bi bi-star-fill" aria-hidden="true"></i>
                                        @endfor
                                    </span>
                                </div>
                                @if ($review->comment)
                                    <p class="mb-0">{{ $review->comment }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="text-secondary mb-0">Chưa có đánh giá cho sản phẩm này.</p>
                @endif
            </section>
        </article>
    </div>

    @if ($relatedProducts->isNotEmpty())
        <section class="store-section store-section-tinted">
            <div class="container">
                <div class="store-section-heading">
                    <div>
                        <span class="store-section-kicker">Cùng danh mục</span>
                        <h2 class="store-section-title">Sản phẩm liên quan</h2>
                    </div>
                    <a class="store-section-link" href="{{ route('products.category', $product->category) }}">
                        Xem danh mục
                        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col">
                            <x-store.product-card :product="$relatedProduct" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
