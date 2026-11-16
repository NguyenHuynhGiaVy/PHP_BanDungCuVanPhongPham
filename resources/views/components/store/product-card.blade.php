@props(['product'])

@php
    $image = $product->images->first();
    $variant = $product->variants->first();
    $inventory = $variant?->inventory;
    $availableQuantity = $inventory
        ? max(0, $inventory->quantity - $inventory->reserved_quantity)
        : null;
    $discountPercentage = $variant && $variant->compare_price > $variant->price
        ? (int) round((1 - $variant->price / $variant->compare_price) * 100)
        : null;
@endphp

<article class="store-product-card">
    <a class="store-product-image" href="{{ route('products.show', $product->slug) }}">
        @if ($discountPercentage)
            <span class="store-product-discount">-{{ $discountPercentage }}%</span>
        @endif
        @if ($image)
            <img
                src="{{ asset($image->image_url) }}"
                alt="{{ $product->name }}"
                loading="lazy"
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
    </a>
    <div class="store-product-details">
        <p class="store-product-category">{{ $product->category?->name }}</p>
        <h3 class="store-product-name">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        <p class="store-product-brand">{{ $product->brand?->name ?? 'Đang cập nhật thương hiệu' }}</p>

        @if ($product->approved_reviews_count > 0)
            <p
                class="store-product-rating"
                role="img"
                aria-label="Đánh giá trung bình {{ number_format((float) $product->approved_reviews_average, 1, ',', '.') }} trên 5 từ {{ $product->approved_reviews_count }} đánh giá"
            >
                <span aria-hidden="true">
                    @for ($star = 1; $star <= 5; $star++)
                        <i class="bi {{ $product->approved_reviews_average >= $star - 0.5 ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                </span>
                <span aria-hidden="true">{{ number_format((float) $product->approved_reviews_average, 1, ',', '.') }}</span>
                <span class="store-product-rating-count" aria-hidden="true">({{ $product->approved_reviews_count }})</span>
            </p>
        @else
            <p class="store-product-rating store-product-rating-empty">Chưa có đánh giá</p>
        @endif

        <div class="store-product-price">
            @if ($variant)
                <span>{{ number_format((float) $variant->price, 0, ',', '.') }} ₫</span>
                @if ($discountPercentage)
                    <del>{{ number_format((float) $variant->compare_price, 0, ',', '.') }} ₫</del>
                @endif
            @else
                <span class="store-product-price-pending">Giá đang cập nhật</span>
            @endif
        </div>

        <p @class([
            'store-product-stock',
            'store-product-stock-pending' => $availableQuantity === null,
            'store-product-stock-out' => $availableQuantity === 0,
            'mb-0',
        ])>
            @if ($availableQuantity === null)
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                Đang cập nhật tồn kho
            @elseif ($availableQuantity > 0)
                <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                Còn hàng ({{ $availableQuantity }})
            @else
                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                Tạm hết hàng
            @endif
        </p>

        <div class="store-product-actions">
            @if ($variant && $availableQuantity > 0)
                @auth
                    <form action="{{ route('cart.items.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn store-product-add" type="submit">
                            <i class="bi bi-bag-plus me-1" aria-hidden="true"></i>
                            Thêm giỏ hàng
                        </button>
                    </form>
                @else
                    <a class="btn store-product-add" href="{{ route('login') }}" aria-label="Đăng nhập để thêm {{ $product->name }} vào giỏ hàng">
                        <i class="bi bi-bag-plus me-1" aria-hidden="true"></i>
                        Thêm giỏ hàng
                    </a>
                @endauth
            @else
                <button class="btn store-product-add" type="button" disabled>
                    <i class="bi bi-bag-plus me-1" aria-hidden="true"></i>
                    Thêm giỏ hàng
                </button>
            @endif

            @if (! request()->routeIs('wishlist.index'))
                @auth
                    <form action="{{ route('wishlist.store', $product) }}" method="POST">
                        @csrf
                        <button
                            class="btn store-product-favorite"
                            type="submit"
                            aria-label="Thêm {{ $product->name }} vào danh sách yêu thích"
                            title="Lưu vào yêu thích"
                        >
                            <i class="bi bi-heart" aria-hidden="true"></i>
                        </button>
                    </form>
                @else
                    <a
                        class="btn store-product-favorite"
                        href="{{ route('login') }}"
                        aria-label="Đăng nhập để lưu {{ $product->name }} vào danh sách yêu thích"
                        title="Đăng nhập để lưu yêu thích"
                    >
                        <i class="bi bi-heart" aria-hidden="true"></i>
                    </a>
                @endauth
            @endif
        </div>
    </div>
</article>
