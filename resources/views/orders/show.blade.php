@extends('layouts.store')

@section('title', 'Đơn hàng #'.$order->order_code.' | Stationery Store')

@section('content')
    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">#{{ $order->order_code }}</li>
            </ol>
        </nav>

        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center rounded-3 shadow-sm border-0 mb-4 p-3" role="status">
                <div class="fs-4 text-success me-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1 fw-bold">Đặt hàng thành công!</h5>
                    <p class="mb-0 text-muted">{{ session('status') }}</p>
                </div>
                <a class="btn btn-sm btn-primary ms-auto rounded-pill px-3" href="{{ route('products.index') }}">
                    <i class="bi bi-bag me-1"></i> Tiếp tục mua sắm
                </a>
            </div>
        @endif

        @php
            $paymentBadgeClass = match($order->payment_status) {
                'PAID' => 'bg-success-subtle text-success border border-success-subtle',
                'PENDING' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'FAILED' => 'bg-danger-subtle text-danger border border-danger-subtle',
                default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            };
            $paymentStatusLabel = match($order->payment_status) {
                'PAID' => 'Đã thanh toán',
                'PENDING' => 'Chờ thanh toán',
                'FAILED' => 'Thanh toán thất bại',
                default => $order->payment_status,
            };
            $paymentMethodLabel = match($order->payment_method) {
                'COD' => 'Thanh toán khi nhận hàng (COD)',
                'VNPAY' => 'Cổng VNPAY',
                'MOCK' => 'Thanh toán thử nghiệm',
                default => $order->payment_method,
            };
            $orderStatusBadge = match($order->order_status) {
                'DELIVERED' => 'bg-success text-white',
                'SHIPPED' => 'bg-primary text-white',
                'PROCESSING' => 'bg-info text-dark',
                'PENDING' => 'bg-warning text-dark',
                'CANCELLED' => 'bg-danger text-white',
                default => 'bg-secondary text-white',
            };
            $orderStatusLabel = match($order->order_status) {
                'DELIVERED' => 'Đã nhận hàng',
                'SHIPPED' => 'Đang giao hàng',
                'PROCESSING' => 'Đang xử lý',
                'PENDING' => 'Chờ xác nhận',
                'CANCELLED' => 'Đã hủy',
                default => $order->order_status,
            };
        @endphp

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 mb-2">Chi tiết đơn hàng</span>
                <h1 class="h3 fw-bold mb-0">Đơn hàng #{{ $order->order_code }}</h1>
                <p class="text-muted small mb-0 mt-1">
                    <i class="bi bi-calendar3 me-1"></i> Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge {{ $orderStatusBadge }} px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-truck me-1"></i> {{ $orderStatusLabel }}
                </span>
                <span class="badge {{ $paymentBadgeClass }} px-3 py-2 rounded-pill fs-7">
                    <i class="bi bi-wallet2 me-1"></i> {{ $paymentStatusLabel }}
                </span>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>
            <div class="col-12 col-lg-9">
                <div class="row g-4 align-items-start">
                    <section class="col-12 col-lg-8">
                        <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                            <h2 class="h5 fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-box-seam text-primary me-2"></i> Sản phẩm đã đặt
                                <span class="badge bg-light text-secondary rounded-pill ms-2 fs-7">{{ $order->items->count() }} món</span>
                            </h2>
                            <div class="order-items-list divide-y">
                                @foreach ($order->items as $item)
                                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center p-2" style="width: 52px; height: 52px; flex-shrink: 0;">
                                                <i class="bi bi-journal-text fs-4 text-primary opacity-75"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">{{ $item->product_name }}</h6>
                                                <div class="small text-muted">
                                                    <span>{{ $item->variant_name }}</span>
                                                    <span class="mx-1">•</span>
                                                    <span>SKU: {{ $item->sku }}</span>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    {{ number_format((float) $item->price, 0, ',', '.') }} ₫ × <span class="fw-semibold text-dark">{{ $item->quantity }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end ps-3">
                                            <span class="fw-bold text-dark fs-6">{{ number_format((float) $item->subtotal, 0, ',', '.') }} ₫</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 pt-2">
                                <div class="d-flex justify-content-between text-muted mb-2 small">
                                    <span>Tạm tính</span>
                                    <span class="text-dark fw-semibold">{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted mb-3 small">
                                    <span>Phí giao hàng</span>
                                    <span class="text-success fw-semibold">Miễn phí</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <span class="fw-bold fs-6">Tổng thanh toán:</span>
                                    <span class="fw-bolder fs-4 text-primary">{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-start">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách đơn hàng
                            </a>
                        </div>
                    </section>

                    <aside class="col-12 col-lg-4">
                        <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                            <h2 class="h6 fw-bold mb-3 d-flex align-items-center text-dark">
                                <i class="bi bi-geo-alt-fill text-danger me-2"></i> Địa chỉ nhận hàng
                            </h2>
                            <p class="mb-1 fw-bold text-dark">{{ $order->receiver_name }}</p>
                            <p class="text-secondary small mb-2"><i class="bi bi-telephone me-1"></i> {{ $order->receiver_phone }}</p>
                            <p class="text-secondary small mb-0"><i class="bi bi-house me-1"></i> {{ $order->shipping_address }}</p>
                        </div>

                        <div class="bg-white rounded-4 shadow-sm border p-4">
                            <h2 class="h6 fw-bold mb-3 d-flex align-items-center text-dark">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i> Phương thức thanh toán
                            </h2>
                            <p class="mb-2 fw-semibold text-dark">{{ $paymentMethodLabel }}</p>
                            <div class="p-2 bg-light rounded-3 mb-2 small">
                                <div class="d-flex justify-content-between text-muted mb-1">
                                    <span>Trạng thái:</span>
                                    <span class="fw-bold text-dark">{{ $paymentStatusLabel }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-muted">
                                    <span>Mã giao dịch:</span>
                                    <span class="fw-semibold text-dark">{{ $order->payments->first()?->transaction_code ?? 'Đang cập nhật' }}</span>
                                </div>
                            </div>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-info-circle me-1"></i> Mọi thắc mắc về đơn hàng, vui lòng liên hệ hotline hỗ trợ.
                            </p>
                        </div>
                        @if ($order->order_status === 'SHIPPED')
                            <form action="{{ route('orders.received', $order) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-success w-100 fw-bold" type="submit">
                                    <i class="bi bi-check2-circle me-1"></i>Xác nhận đã nhận hàng
                                </button>
                            </form>
                        @elseif ($order->order_status === 'DELIVERED')
                            <div class="alert alert-success d-flex align-items-center gap-2 mt-3 mb-0 small">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Đơn hàng đã được xác nhận là đã nhận. Bạn có thể đánh giá sản phẩm.</span>
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        </div>
    </div>
@endsection
