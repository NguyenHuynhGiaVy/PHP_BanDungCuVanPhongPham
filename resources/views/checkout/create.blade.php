@extends('layouts.store')

@section('title', 'Thanh toán & Đặt hàng | Việt Thịnh')

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán & Đặt hàng</li>
            </ol>
        </nav>

        <div class="mb-4">
            <h1 class="h4 fw-bold text-dark mb-1">Xác nhận thông tin & Đặt hàng</h1>
            <p class="text-muted small mb-0">Vui lòng kiểm tra kỹ địa chỉ nhận hàng và chọn phương thức thanh toán phù hợp</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Vui lòng kiểm tra lại:</div>
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
                <!-- CỘT TRÁI: THÔNG TIN GIAO HÀNG & PHƯƠNG THỨC THANH TOÁN -->
                <div class="col-12 col-lg-7">
                    <!-- 1. THÔNG TIN GIAO HÀNG -->
                    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">1</span>
                            <h2 class="h5 fw-bold text-dark mb-0">Thông tin nhận hàng</h2>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-name">Họ và tên người nhận <span class="text-danger">*</span></label>
                                <input
                                    class="form-control"
                                    id="checkout-name"
                                    name="receiver_name"
                                    value="{{ old('receiver_name', $defaultAddress?->receiver_name ?? auth()->user()->full_name) }}"
                                    autocomplete="name"
                                    maxlength="100"
                                    placeholder="Ví dụ: Nguyễn Văn A"
                                    required
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-phone">Số điện thoại <span class="text-danger">*</span></label>
                                <input
                                    class="form-control"
                                    id="checkout-phone"
                                    name="phone"
                                    type="tel"
                                    value="{{ old('phone', $defaultAddress?->phone ?? auth()->user()->phone) }}"
                                    autocomplete="tel"
                                    inputmode="tel"
                                    maxlength="20"
                                    placeholder="Ví dụ: 0912 345 678"
                                    required
                                >
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-province">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                                <input
                                    class="form-control"
                                    id="checkout-province"
                                    name="province"
                                    value="{{ old('province', $defaultAddress?->province) }}"
                                    autocomplete="address-level1"
                                    maxlength="100"
                                    placeholder="TP. Hồ Chí Minh"
                                    required
                                >
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-district">Quận / Huyện <span class="text-danger">*</span></label>
                                <input
                                    class="form-control"
                                    id="checkout-district"
                                    name="district"
                                    value="{{ old('district', $defaultAddress?->district) }}"
                                    autocomplete="address-level2"
                                    maxlength="100"
                                    placeholder="Quận 1"
                                    required
                                >
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-ward">Phường / Xã <span class="text-danger">*</span></label>
                                <input
                                    class="form-control"
                                    id="checkout-ward"
                                    name="ward"
                                    value="{{ old('ward', $defaultAddress?->ward) }}"
                                    autocomplete="address-level3"
                                    maxlength="100"
                                    placeholder="Phường Bến Thành"
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-address">Địa chỉ cụ thể (Số nhà, tên đường, tòa nhà...) <span class="text-danger">*</span></label>
                                <input
                                    class="form-control"
                                    id="checkout-address"
                                    name="detail_address"
                                    value="{{ old('detail_address', $defaultAddress?->detail_address) }}"
                                    autocomplete="street-address"
                                    maxlength="255"
                                    placeholder="Ví dụ: 123 Bùi Thị Xuân"
                                    required
                                >
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-secondary" for="checkout-note">Ghi chú giao hàng (Không bắt buộc)</label>
                                <textarea class="form-control" id="checkout-note" name="note" rows="2" maxlength="1000" placeholder="Chỉ dẫn giao hàng ví dụ: giao giờ hành chính...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. PHƯƠNG THỨC THANH TOÁN (NỔI BẬT VNPAY & COD) -->
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">2</span>
                            <h2 class="h5 fw-bold text-dark mb-0">Phương thức thanh toán</h2>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <!-- Option 1: VNPAY (Khuyên dùng) -->
                            <label class="payment-option-card p-3 border rounded-3 d-block @if(old('payment_method', 'VNPAY') === 'VNPAY') selected @endif">
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" value="VNPAY" @checked(old('payment_method', 'VNPAY') === 'VNPAY')>
                                    <div class="d-flex align-items-center justify-content-center bg-white border rounded-2 px-2 py-1 shadow-sm" style="min-width: 58px; height: 40px;">
                                        <span class="fw-bold" style="color: #005baa; font-size: 0.95rem; letter-spacing: -0.5px;">VN<span style="color: #ed1c24;">PAY</span></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-dark">Cổng thanh toán online VNPAY</strong>
                                            <span class="badge bg-danger" style="font-size: 0.65rem;">Khuyên dùng</span>
                                        </div>
                                        <small class="text-muted d-block mt-0">Quét mã VNPAY-QR, Thẻ ATM nội địa hoặc thẻ quốc tế Visa / Mastercard / JCB.</small>
                                    </div>
                                </div>
                            </label>

                            <!-- Option 2: COD -->
                            <label class="payment-option-card p-3 border rounded-3 d-block @if(old('payment_method') === 'COD') selected @endif">
                                <div class="d-flex align-items-center gap-3">
                                    <input class="form-check-input mt-0" type="radio" name="payment_method" value="COD" @checked(old('payment_method') === 'COD')>
                                    <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-2" style="width: 58px; height: 40px;">
                                        <i class="bi bi-cash-stack fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong class="text-dark d-block">Thanh toán khi nhận hàng (COD)</strong>
                                        <small class="text-muted d-block mt-0">Bạn chỉ thanh toán bằng tiền mặt khi shipper giao hàng tận nơi.</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG (STICKY) -->
                <aside class="col-12 col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 p-4 position-sticky" style="top: 1rem;">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h2 class="h5 fw-bold text-dark mb-0">Đơn hàng của bạn</h2>
                            <span class="badge bg-light text-muted border">{{ $cart->items->sum('quantity') }} sản phẩm</span>
                        </div>

                        <!-- Danh sách sản phẩm mua -->
                        <div class="d-flex flex-column gap-3 mb-3" style="max-height: 280px; overflow-y: auto;">
                            @foreach ($cart->items as $item)
                                @php
                                    $product = $item->variant->product;
                                    $productImage = $product->images->first();
                                @endphp
                                <div class="d-flex align-items-center gap-3 pb-2 border-bottom">
                                    <div class="rounded border p-1 bg-white" style="width: 54px; height: 54px; flex-shrink: 0;">
                                        @if ($productImage)
                                            <img src="{{ asset($productImage->image_url) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: contain;">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-box-seam"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 text-truncate small fw-bold text-dark" title="{{ $product->name }}">{{ $product->name }}</h6>
                                        <small class="text-muted d-block" style="font-size: 0.78rem;">
                                            {{ $item->variant->variant_name }} &times; <strong>{{ $item->quantity }}</strong>
                                        </small>
                                    </div>
                                    <div class="text-end fw-semibold text-dark small" style="white-space: nowrap;">
                                        {{ number_format((float) $item->variant->price * $item->quantity, 0, ',', '.') }} ₫
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Chi tiết tính tiền -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary" for="coupon_code">Mã khuyến mãi</label>
                            <input
                                class="form-control"
                                id="coupon_code"
                                name="coupon_code"
                                value="{{ old('coupon_code') }}"
                                list="available-coupon-codes"
                                maxlength="50"
                                placeholder="Nhập hoặc chọn mã ưu đãi"
                            >
                            <datalist id="available-coupon-codes">
                                @foreach ($availableCoupons as $availableCoupon)
                                    <option value="{{ $availableCoupon->code }}"></option>
                                @endforeach
                            </datalist>
                            @error('coupon_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-flex justify-content-between text-secondary small mb-2">
                            <span>Tạm tính</span>
                            <span class="text-dark fw-semibold">{{ number_format($cartTotal, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="d-flex justify-content-between text-secondary small mb-3">
                            <span>Phí vận chuyển</span>
                            <span class="text-success fw-semibold">Miễn phí</span>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fw-bold text-dark">Tổng thanh toán:</span>
                            <span class="fs-4 fw-bold text-danger">{{ number_format($cartTotal, 0, ',', '.') }} ₫</span>
                        </div>

                        <!-- Nút Xác nhận đặt hàng -->
                        <button class="btn btn-primary w-100 py-3 fw-bold fs-6 rounded-3 shadow-sm" type="submit" data-checkout-submit style="background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%); border: none;">
                            <span data-checkout-submit-label>XÁC NHẬN ĐẶT HÀNG</span>
                        </button>
                        <span class="visually-hidden" role="status" aria-live="polite" data-checkout-status></span>

                        <div class="text-center mt-3 text-muted small" style="font-size: 0.75rem;">
                            Nhấn "Xác nhận đặt hàng" đồng nghĩa với việc bạn đồng ý với các điều khoản mua sắm của Việt Thịnh.
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            paymentRadios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    document.querySelectorAll('.payment-option-card').forEach(function (card) {
                        card.classList.remove('selected');
                    });
                    const parentCard = radio.closest('.payment-option-card');
                    if (parentCard) {
                        parentCard.classList.add('selected');
                    }
                });
            });
        });
    </script>
@endsection
