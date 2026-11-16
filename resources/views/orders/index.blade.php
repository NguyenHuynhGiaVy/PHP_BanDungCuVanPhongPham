@extends('layouts.store')

@section('title', 'Lịch sử đơn hàng | Stationery Store')

@section('content')
    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.show') }}">Tài khoản</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn hàng</li>
            </ol>
        </nav>
        <div class="store-section-heading">
            <div>
                <span class="store-section-kicker">Theo dõi các lần mua sắm</span>
                <h1 class="store-section-title">Lịch sử đơn hàng</h1>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>
            <div class="col-12 col-lg-9">
                @if ($orders->isNotEmpty())
                    <section class="store-panel">
                        @foreach ($orders as $order)
                            <a class="store-account-order" href="{{ route('orders.show', $order) }}">
                                <span>
                                    <strong>{{ $order->order_code }}</strong>
                                    <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </span>
                                <span class="text-end">
                                    <strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</strong>
                                    <small>{{ $order->order_status }} · {{ $order->payment_status }}</small>
                                </span>
                            </a>
                        @endforeach
                    </section>
                    <div class="store-pagination mt-4">{{ $orders->links() }}</div>
                @else
                    <div class="store-empty-state">
                        <span class="store-empty-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
                        <h2>Bạn chưa có đơn hàng nào</h2>
                        <p class="mb-0">Các đơn hàng đã đặt sẽ hiển thị tại đây.</p>
                        <a class="btn btn-primary mt-2" href="{{ route('products.index') }}">Mua sắm ngay</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
