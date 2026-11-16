@extends('layouts.store')

@section('title', 'Đơn hàng '.$order->order_code.' | Stationery Store')

@section('content')
    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $order->order_code }}</li>
            </ol>
        </nav>
        @if (session('status'))
            <div class="store-order-success mb-4" role="status">
                <span class="store-order-success-icon">
                    <i class="bi bi-check2-circle" aria-hidden="true"></i>
                </span>
                <div>
                    <h2 class="h4 mb-1">Đặt hàng thành công</h2>
                    <p class="mb-0">{{ session('status') }}</p>
                </div>
                <a class="btn btn-primary ms-lg-auto" href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
            </div>
        @endif
        <div class="store-section-heading">
            <div>
                <span class="store-section-kicker">Chi tiết đơn hàng</span>
                <h1 class="store-section-title">{{ $order->order_code }}</h1>
            </div>
            <span class="badge {{ $order->payment_status === 'PAID' ? 'text-bg-success' : 'text-bg-secondary' }}">
                {{ $order->payment_status === 'PAID' ? 'Thanh toán mô phỏng thành công' : 'Đang cập nhật thanh toán' }}
            </span>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>
            <div class="col-12 col-lg-9">
                <div class="row g-4 align-items-start">
                    <section class="col-12 col-lg-8">
                        <div class="store-panel">
                            <h2 class="h5 fw-bold mb-3">Sản phẩm đã đặt</h2>
                            @foreach ($order->items as $item)
                                <div class="store-order-item">
                                    <div>
                                        <strong>{{ $item->product_name }}</strong>
                                        <span>{{ $item->variant_name }} · SKU {{ $item->sku }}</span>
                                        <span>{{ number_format((float) $item->price, 0, ',', '.') }} ₫ × {{ $item->quantity }}</span>
                                    </div>
                                    <strong>{{ number_format((float) $item->subtotal, 0, ',', '.') }} ₫</strong>
                                </div>
                            @endforeach
                            <hr>
                            <div class="store-cart-total">
                                <span>Tổng cộng</span>
                                <strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</strong>
                            </div>
                        </div>
                    </section>
                    <aside class="col-12 col-lg-4">
                        <div class="store-panel">
                            <h2 class="h5 fw-bold mb-3">Giao hàng</h2>
                            <p class="mb-1"><strong>{{ $order->receiver_name }}</strong></p>
                            <p class="text-secondary">{{ $order->receiver_phone }}<br>{{ $order->shipping_address }}</p>
                            <hr>
                            <p class="mb-1"><strong>Thanh toán mô phỏng</strong></p>
                            <p class="text-secondary small mb-0">
                                Mã giao dịch: {{ $order->payments->first()?->transaction_code ?? 'Đang cập nhật' }}
                            </p>
                            <p class="text-secondary small mt-2 mb-0">
                                {{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->order_status }}
                            </p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
@endsection
