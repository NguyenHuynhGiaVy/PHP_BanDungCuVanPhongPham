@extends('layouts.store')

@section('title', 'Giỏ hàng của bạn | Stationery Store')

@section('content')
    @php
        $cartTotal = $cart?->items->sum(fn ($item) => (float) $item->variant->price * $item->quantity) ?? 0;
        $totalQuantity = $cart?->items->sum('quantity') ?? 0;
    @endphp

    <div class="container py-4 py-lg-5">
        <!-- Breadcrumb -->
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark mb-0">Giỏ hàng</h1>
            </div>
            @if ($cart?->items->isNotEmpty())
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-bag-check me-1"></i> Có <strong>{{ $totalQuantity }}</strong> sản phẩm trong giỏ
                </span>
            @endif
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-3 shadow-sm border-0 mb-4 p-3" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2"></i>
                    <strong class="small">Vui lòng kiểm tra lại:</strong>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($cart?->items->isNotEmpty())
            <div class="row g-4 align-items-start">
                <!-- CỘT TRÁI: DANH SÁCH SẢN PHẨM -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-secondary small fw-bold text-uppercase border-bottom">
                                    <tr>
                                        <th scope="col" class="ps-4 py-3" style="min-width: 280px;">Sản phẩm</th>
                                        <th scope="col" class="py-3 text-center" style="min-width: 110px;">Đơn giá</th>
                                        <th scope="col" class="py-3 text-center" style="min-width: 130px;">Số lượng</th>
                                        <th scope="col" class="py-3 text-end" style="min-width: 120px;">Thành tiền</th>
                                        <th scope="col" class="pe-4 py-3 text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($cart->items as $item)
                                        @php
                                            $variant = $item->variant;
                                            $product = $variant->product;
                                            $productImage = $product->images->first();
                                            $availableQuantity = $variant->inventory
                                                ? max(0, $variant->inventory->quantity - $variant->inventory->reserved_quantity)
                                                : null;
                                            $itemSubtotal = (float) $variant->price * $item->quantity;
                                        @endphp
                                        <tr class="cart-row">
                                            <!-- Sản phẩm -->
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <a href="{{ route('products.show', $product->slug) }}" class="flex-shrink-0 d-block rounded-3 border bg-light p-1" style="width: 68px; height: 68px;">
                                                        @if ($productImage)
                                                            <img
                                                                src="{{ asset($productImage->image_url) }}"
                                                                alt="{{ $product->name }}"
                                                                class="w-100 h-100 object-fit-contain rounded-2"
                                                                loading="lazy"
                                                            >
                                                        @else
                                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary">
                                                                <i class="bi bi-box-seam fs-4"></i>
                                                            </div>
                                                        @endif
                                                    </a>
                                                    <div>
                                                        <a href="{{ route('products.show', $product->slug) }}" class="fw-bold text-dark text-decoration-none small d-block mb-1 line-clamp-2">
                                                            {{ $product->name }}
                                                        </a>
                                                        <div class="small text-muted d-flex align-items-center gap-2">
                                                            <span class="badge bg-light text-secondary border">{{ $variant->variant_name }}</span>
                                                            <span class="text-secondary" style="font-size: 0.75rem;">SKU: {{ $variant->sku }}</span>
                                                        </div>
                                                        @if ($availableQuantity !== null && $availableQuantity < $item->quantity)
                                                            <span class="text-danger small d-block mt-1">
                                                                <i class="bi bi-exclamation-circle me-1"></i> Số lượng vượt tồn kho (còn {{ $availableQuantity }})
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Đơn giá -->
                                            <td class="py-3 text-center">
                                                <span class="fw-semibold text-dark small">
                                                    {{ number_format((float) $variant->price, 0, ',', '.') }} ₫
                                                </span>
                                            </td>

                                            <!-- Số lượng: Chỉ bấm + và -, không có mũi tên spin, tự động cập nhật -->
                                            <td class="py-3 text-center">
                                                <form
                                                    action="{{ route('cart.items.update', $item) }}"
                                                    method="POST"
                                                    class="d-inline-flex align-items-center justify-content-center"
                                                    data-cart-quantity-form
                                                >
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="d-inline-flex align-items-center border rounded-3 bg-white overflow-hidden shadow-sm" style="height: 34px;">
                                                        <button
                                                            class="btn btn-sm btn-light text-secondary text-decoration-none px-2 h-100 d-flex align-items-center justify-content-center border-0 border-end"
                                                            type="button"
                                                            aria-label="Giảm số lượng"
                                                            data-quantity-step="-1"
                                                            style="width: 32px;"
                                                        >
                                                            <i class="bi bi-dash fw-bold"></i>
                                                        </button>
                                                        <input
                                                            class="border-0 text-center fw-bold text-dark no-spinner px-0 h-100"
                                                            id="cart-quantity-{{ $item->id }}"
                                                            name="quantity"
                                                            type="number"
                                                            value="{{ $item->quantity }}"
                                                            min="1"
                                                            max="99"
                                                            data-available="{{ $availableQuantity ?? '' }}"
                                                            data-cart-quantity
                                                            readonly
                                                            style="width: 44px; background: transparent; font-size: 0.9rem; user-select: none; cursor: default;"
                                                            required
                                                        >
                                                        <button
                                                            class="btn btn-sm btn-light text-secondary text-decoration-none px-2 h-100 d-flex align-items-center justify-content-center border-0 border-start"
                                                            type="button"
                                                            aria-label="Tăng số lượng"
                                                            data-quantity-step="1"
                                                            style="width: 32px;"
                                                        >
                                                            <i class="bi bi-plus fw-bold"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>

                                            <!-- Thành tiền -->
                                            <td class="py-3 text-end">
                                                <span class="fw-bold text-danger fs-6">
                                                    {{ number_format($itemSubtotal, 0, ',', '.') }} ₫
                                                </span>
                                            </td>

                                            <!-- Nút xóa -->
                                            <td class="pe-4 py-3 text-center">
                                                <form
                                                    id="remove-cart-item-{{ $item->id }}"
                                                    action="{{ route('cart.items.destroy', $item) }}"
                                                    method="POST"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="btn btn-sm btn-outline-danger border-0 rounded-circle"
                                                        type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cart-remove-modal"
                                                        data-remove-form="remove-cart-item-{{ $item->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        title="Xóa khỏi giỏ"
                                                        style="width: 32px; height: 32px; padding: 0;"
                                                    >
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-arrow-left me-1"></i> Tiếp tục chọn mua thêm
                        </a>
                    </div>
                </div>

                <!-- CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & NÚT THANH TOÁN (STICKY) -->
                <aside class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 position-sticky" style="top: 100px;">
                        <h2 class="h5 fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                            <span>Tóm tắt đơn hàng</span>
                            <i class="bi bi-receipt text-primary"></i>
                        </h2>

                        <div class="d-flex justify-content-between text-muted mb-2 small">
                            <span>Tạm tính ({{ $totalQuantity }} món):</span>
                            <span class="text-dark fw-semibold">{{ number_format($cartTotal, 0, ',', '.') }} ₫</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted mb-2 small">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success fw-semibold">Miễn phí giao hàng</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted mb-3 small">
                            <span>Giảm giá:</span>
                            <span class="text-dark fw-semibold">0 ₫</span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="fw-bold fs-6 text-dark d-block">Tổng thanh toán:</span>
                                <small class="text-muted" style="font-size: 0.75rem;">(Đã bao gồm VAT nếu có)</small>
                            </div>
                            <span class="fs-4 fw-bolder text-danger">
                                {{ number_format($cartTotal, 0, ',', '.') }} ₫
                            </span>
                        </div>

                        <!-- NÚT TIẾN HÀNH THANH TOÁN -->
                        <a
                            class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow d-flex align-items-center justify-content-center gap-2 text-uppercase"
                            href="{{ route('checkout.create') }}"
                            style="letter-spacing: 0.5px; background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); border: none;"
                        >
                            <span>Tiến hành thanh toán</span>
                            <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                        </a>
                    </div>
                </aside>
            </div>
        @else
            <!-- GIỎ HÀNG TRỐNG -->
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 my-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle text-primary mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-cart-x fs-1"></i>
                </div>
                <h2 class="h4 fw-bold text-dark mb-2">Giỏ hàng của bạn đang trống</h2>
                <p class="text-muted small mb-4">Hãy dạo quanh cửa hàng và chọn những món văn phòng phẩm, dụng cụ học tập ưng ý nhé!</p>
                <div>
                    <a class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" href="{{ route('products.index') }}">
                        <i class="bi bi-bag me-1"></i> Khám phá sản phẩm ngay
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- MODAL XÁC NHẬN XÓA SẢN PHẨM KHỎI GIỎ HÀNG -->
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
                <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-0 py-3">
                        <h5 class="modal-title fw-bold text-dark fs-6" id="cart-remove-modal-title">
                            <i class="bi bi-exclamation-circle text-danger me-2"></i> Xác nhận xóa sản phẩm
                        </h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body py-4">
                        <p class="mb-0 text-secondary">
                            Bạn có chắc chắn muốn bỏ sản phẩm <strong class="text-dark" data-remove-product-name></strong> ra khỏi giỏ hàng không?
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button class="btn btn-outline-secondary rounded-pill px-4" type="button" data-bs-dismiss="modal">Giữ lại</button>
                        <button class="btn btn-danger rounded-pill px-4" type="button" data-cart-remove-confirm>Đồng ý xóa</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
