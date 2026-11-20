@props(['product'])

@php
$image = $product->images->first();
$variant = $product->variants->first();
$inventory = $variant?->inventory;
$availableQuantity = $inventory ? max(0, $inventory->quantity - $inventory->reserved_quantity) : null;
@endphp

<article class="store-product-card">
    <a class="store-product-image" href="{{ route('products.show', $product->slug) }}">
        @if ($image)
        <img src="{{ str_starts_with($image->image_url, 'http') ? $image->image_url : asset($image->image_url) }}" alt="{{ $product->name }}" loading="lazy" data-product-image>
        <span class="store-product-image-fallback" data-product-image-fallback hidden>
            <i class="bi bi-box-seam" aria-hidden="true"></i>
        </span>
        @else
        <span class="store-product-image-fallback"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
        @endif
    </a>

    <div class="store-product-details">
        <div class="mb-1">
            <span class="badge" style="background-color: #f0f5ff; color: #1e40af; font-weight: 500; font-size: 0.7rem; padding: 0.35rem 0.5rem;">
                Đã bán: {{ $product->sold_count ?? rand(1, 99).'.' . rand(1,9) . 'k' }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-1 mb-1" style="color: #f59e0b; font-size: 0.75rem;">
            @php
                $approvedReviewCount = (int) ($product->approved_reviews_count ?? 0);
                $avg = $approvedReviewCount > 0 ? (float) ($product->approved_reviews_average ?? 0) : 0;
            @endphp
            @for ($star = 1; $star <= 5; $star++)
                <i class="bi {{ $avg >= $star - 0.5 ? 'bi-star-fill' : 'bi-star' }}"></i>
                @endfor
                <span class="text-muted ms-1" style="font-size: 0.7rem;">({{ $approvedReviewCount }})</span>
        </div>

        <h3 class="store-product-name"><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h3>

        <div class="store-product-price">
            @if ($variant)
            <span>{{ number_format((float) $variant->price, 0, ',', '.') }}₫</span>
            @if($variant->compare_price > $variant->price)
            <del>{{ number_format((float) $variant->compare_price, 0, ',', '.') }}₫</del>
            @endif
            @else
            <span class="store-product-price-pending">Giá đang cập nhật</span>
            @endif
        </div>

        <div class="store-product-actions mt-auto">
            @if ($variant && $availableQuantity > 0)
                @auth
                    <!-- Nút Thêm vào giỏ -->
                    <form action="{{ route('cart.items.store') }}" method="POST" class="flex-fill">
                        @csrf
                        <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="purchase_intent" value="cart">
                        <button class="btn btn-outline-primary w-100" type="submit">
                            <i class="bi bi-cart-plus"></i> Thêm
                        </button>
                    </form>

                    <!-- Nút Mua ngay (Chuyển thẳng sang trang thanh toán) -->
                    <form action="{{ route('cart.items.store') }}" method="POST" class="flex-fill">
                        @csrf
                        <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="purchase_intent" value="checkout">
                        <button class="btn btn-primary w-100" type="submit">
                            Mua ngay
                        </button>
                    </form>
                @else
                    <a class="btn btn-outline-primary flex-fill" href="{{ route('login') }}">
                        <i class="bi bi-cart-plus"></i> Thêm
                    </a>
                    <a class="btn btn-primary flex-fill" href="{{ route('login') }}">
                        Mua ngay
                    </a>
                @endauth
            @else
                <button class="btn btn-secondary w-100" type="button" disabled>
                    Hết hàng
                </button>
            @endif
        </div>
    </div>
</article>