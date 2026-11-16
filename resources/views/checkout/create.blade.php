@extends('layouts.store')

@section('title', 'Thanh toán mô phỏng | Stationery Store')

@section('content')
    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
        <div class="store-section-heading">
            <div>
                <span class="store-section-kicker">Hoàn tất đơn hàng</span>
                <h1 class="store-section-title">Thanh toán mô phỏng</h1>
            </div>
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

        <form action="{{ route('checkout.store') }}" method="POST" data-checkout-form>
            @csrf
            <div class="row g-4 align-items-start">
                <section class="col-12 col-lg-7">
                    <div class="store-panel">
                        <h2 class="h5 fw-bold mb-3">Thông tin giao hàng</h2>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="checkout-name">Người nhận</label>
                                <input
                                    class="form-control"
                                    id="checkout-name"
                                    name="receiver_name"
                                    value="{{ old('receiver_name', $defaultAddress?->receiver_name ?? auth()->user()->full_name) }}"
                                    autocomplete="name"
                                    maxlength="100"
                                    required
                                >
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="checkout-phone">Số điện thoại</label>
                                <input
                                    class="form-control"
                                    id="checkout-phone"
                                    name="phone"
                                    type="tel"
                                    value="{{ old('phone', $defaultAddress?->phone ?? auth()->user()->phone) }}"
                                    autocomplete="tel"
                                    inputmode="tel"
                                    maxlength="20"
                                    required
                                >
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="checkout-province">Tỉnh / thành phố</label>
                                <input
                                    class="form-control"
                                    id="checkout-province"
                                    name="province"
                                    value="{{ old('province', $defaultAddress?->province) }}"
                                    autocomplete="address-level1"
                                    maxlength="100"
                                    required
                                >
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="checkout-district">Quận / huyện</label>
                                <input
                                    class="form-control"
                                    id="checkout-district"
                                    name="district"
                                    value="{{ old('district', $defaultAddress?->district) }}"
                                    autocomplete="address-level2"
                                    maxlength="100"
                                    required
                                >
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="checkout-ward">Phường / xã</label>
                                <input
                                    class="form-control"
                                    id="checkout-ward"
                                    name="ward"
                                    value="{{ old('ward', $defaultAddress?->ward) }}"
                                    autocomplete="address-level3"
                                    maxlength="100"
                                    required
                                >
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="checkout-address">Địa chỉ cụ thể</label>
                                <input
                                    class="form-control"
                                    id="checkout-address"
                                    name="detail_address"
                                    value="{{ old('detail_address', $defaultAddress?->detail_address) }}"
                                    autocomplete="street-address"
                                    maxlength="255"
                                    required
                                >
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="checkout-note">Ghi chú đơn hàng (không bắt buộc)</label>
                                <textarea class="form-control" id="checkout-note" name="note" rows="3" maxlength="1000">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="col-12 col-lg-5">
                    <div class="store-panel">
                        <h2 class="h5 fw-bold mb-3">Đơn hàng của bạn</h2>
                        <div class="store-checkout-items">
                            @foreach ($cart->items as $item)
                                @php
                                    $product = $item->variant->product;
                                    $productImage = $product->images->first();
                                @endphp
                                <div class="store-checkout-item">
                                    <a
                                        class="store-checkout-item-image"
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
                                    <div class="store-checkout-item-info">
                                        <strong>{{ $product->name }}</strong>
                                        <small class="text-secondary">
                                            {{ $item->variant->variant_name }} · {{ $item->quantity }} sản phẩm
                                        </small>
                                    </div>
                                    <strong>
                                        {{ number_format((float) $item->variant->price * $item->quantity, 0, ',', '.') }} ₫
                                    </strong>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        <div class="store-cart-total">
                            <span>Tạm tính</span>
                            <strong>{{ number_format($cartTotal, 0, ',', '.') }} ₫</strong>
                        </div>
                        <div class="store-coupon-preview mt-3">
                            <label class="form-label fw-semibold" for="checkout-coupon">Mã giảm giá</label>
                            <div class="input-group">
                                <input
                                    class="form-control"
                                    id="checkout-coupon"
                                    type="text"
                                    placeholder="Chưa hỗ trợ trong bản demo"
                                    disabled
                                >
                                <button class="btn btn-outline-secondary" type="button" disabled>Áp dụng</button>
                            </div>
                            <p class="small text-secondary mb-0 mt-2">
                                Mã giảm giá chưa được áp dụng trong luồng thanh toán mô phỏng.
                            </p>
                        </div>
                        <div class="store-cart-total mt-2">
                            <span>Phí vận chuyển</span>
                            <strong>0 ₫</strong>
                        </div>
                        <div class="store-cart-total store-checkout-grand-total mt-3">
                            <span>Tổng cộng</span>
                            <strong>{{ number_format($cartTotal, 0, ',', '.') }} ₫</strong>
                        </div>

                        <div class="alert alert-info small mt-4 mb-3">
                            Đây là thanh toán mô phỏng cho đồ án. Không nhập thông tin thẻ và không có khoản tiền thật nào được thu.
                        </div>
                        <div class="store-payment-method mb-3">
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                            <span>
                                <strong>Phương thức thanh toán</strong>
                                <small>Thanh toán mô phỏng · Không thu tiền thật</small>
                            </span>
                        </div>
                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                id="mock-payment-confirmed"
                                name="mock_payment_confirmed"
                                type="checkbox"
                                value="1"
                                @checked(old('mock_payment_confirmed'))
                                required
                            >
                            <label class="form-check-label small" for="mock-payment-confirmed">
                                Tôi xác nhận tạo đơn hàng bằng phương thức thanh toán mô phỏng.
                            </label>
                        </div>
                        <button class="btn btn-primary w-100" type="submit" data-checkout-submit>
                            <span data-checkout-submit-label>Xác nhận đơn hàng</span>
                            <i class="bi bi-check2-circle ms-1" aria-hidden="true"></i>
                        </button>
                        <span class="visually-hidden" role="status" aria-live="polite" data-checkout-status></span>
                    </div>
                </aside>
            </div>
        </form>
    </div>
@endsection
