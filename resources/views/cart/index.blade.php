@extends('layouts.store')

@section('title', 'Giỏ hàng | Stationery Store')

@section('content')
    @php
        $cartTotal = $cart?->items->sum(fn ($item) => (float) $item->variant->price * $item->quantity) ?? 0;
    @endphp

    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>
        <div class="store-section-heading">
            <div>
                <span class="store-section-kicker">Sẵn sàng cho đơn hàng của bạn</span>
                <h1 class="store-section-title">Giỏ hàng</h1>
            </div>
            @if ($cart?->items->isNotEmpty())
                <span class="store-listing-count">{{ $cart->items->sum('quantity') }} sản phẩm</span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($cart?->items->isNotEmpty())
            <div class="row g-4 align-items-start">
                <div class="col-12 col-lg-8">
                    <div class="store-panel p-0">
                        @foreach ($cart->items as $item)
                            @php
                                $variant = $item->variant;
                                $product = $variant->product;
                                $productImage = $product->images->first();
                                $availableQuantity = $variant->inventory
                                    ? max(0, $variant->inventory->quantity - $variant->inventory->reserved_quantity)
                                    : null;
                            @endphp
                            <article class="store-cart-item">
                                <a
                                    class="store-cart-item-image"
                                    href="{{ route('products.show', $product->slug) }}"
                                    aria-label="Xem {{ $product->name }}"
                                >
                                    @if ($productImage)
                                        <img
                                            src="{{ asset($productImage->image_url) }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                            data-product-image
                                        >
                                        <span class="store-product-image-fallback" hidden>
                                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                                        </span>
                                    @else
                                        <span class="store-product-image-fallback">
                                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </a>
                                <div class="store-cart-item-info">
                                    <a class="fw-bold" href="{{ route('products.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                    <span>{{ $variant->variant_name }} · SKU {{ $variant->sku }}</span>
                                    <span>Đơn giá: {{ number_format((float) $variant->price, 0, ',', '.') }} ₫</span>
                                    <strong>
                                        Thành tiền: {{ number_format((float) $variant->price * $item->quantity, 0, ',', '.') }} ₫
                                    </strong>
                                    @if ($availableQuantity === null || $availableQuantity < $item->quantity)
                                        <span class="text-danger">
                                            {{ $availableQuantity === null ? 'Tồn kho đang cập nhật.' : 'Số lượng trong giỏ vượt tồn kho hiện tại.' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="store-cart-item-actions">
                                    <form
                                        action="{{ route('cart.items.update', $item) }}"
                                        method="POST"
                                        class="store-cart-quantity"
                                        data-cart-quantity-form
                                    >
                                        @csrf
                                        @method('PUT')
                                        <label class="visually-hidden" for="cart-quantity-{{ $item->id }}">Số lượng</label>
                                        <button
                                            class="btn btn-outline-secondary btn-sm"
                                            type="button"
                                            aria-label="Giảm số lượng"
                                            data-quantity-step="-1"
                                        >
                                            <i class="bi bi-dash" aria-hidden="true"></i>
                                        </button>
                                        <input
                                            class="form-control form-control-sm"
                                            id="cart-quantity-{{ $item->id }}"
                                            name="quantity"
                                            type="number"
                                            value="{{ $item->quantity }}"
                                            min="1"
                                            max="99"
                                            data-available="{{ $availableQuantity ?? '' }}"
                                            data-cart-quantity
                                            required
                                        >
                                        <button
                                            class="btn btn-outline-secondary btn-sm"
                                            type="button"
                                            aria-label="Tăng số lượng"
                                            data-quantity-step="1"
                                        >
                                            <i class="bi bi-plus" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" type="submit">Cập nhật</button>
                                    </form>
                                    <form
                                        id="remove-cart-item-{{ $item->id }}"
                                        action="{{ route('cart.items.destroy', $item) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            class="btn btn-link btn-sm text-danger"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cart-remove-modal"
                                            data-remove-form="remove-cart-item-{{ $item->id }}"
                                            data-product-name="{{ $product->name }}"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="col-12 col-lg-4">
                    <div class="store-panel">
                        <h2 class="h5 fw-bold mb-3">Tóm tắt đơn hàng</h2>
                        <div class="store-cart-total">
                            <span>Tạm tính</span>
                            <strong>{{ number_format($cartTotal, 0, ',', '.') }} ₫</strong>
                        </div>
                        <p class="store-detail-notice mt-3">
                            Phí vận chuyển: 0 ₫ trong bản demo đồ án.
                        </p>
                        <a class="btn btn-primary w-100 mt-2" href="{{ route('checkout.create') }}">
                            Tiếp tục thanh toán
                            <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                        <a class="store-filter-reset" href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
                    </div>
                </aside>
            </div>
        @else
            <div class="store-empty-state">
                <span class="store-empty-icon"><i class="bi bi-bag" aria-hidden="true"></i></span>
                <h2>Giỏ hàng của bạn đang trống</h2>
                <p class="mb-0">Khám phá sản phẩm và thêm món đồ bạn cần vào giỏ.</p>
                <a class="btn btn-primary mt-2" href="{{ route('products.index') }}">Khám phá sản phẩm</a>
            </div>
        @endif
    </div>

    @if ($cart?->items->isNotEmpty())
        <div
            class="modal fade"
            id="cart-remove-modal"
            tabindex="-1"
            aria-labelledby="cart-remove-modal-title"
            aria-hidden="true"
            data-cart-remove-modal
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="cart-remove-modal-title">Xóa sản phẩm khỏi giỏ?</h2>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc muốn xóa <strong data-remove-product-name></strong> khỏi giỏ hàng?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Giữ lại</button>
                        <button class="btn btn-danger" type="button" data-cart-remove-confirm>Xóa sản phẩm</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
