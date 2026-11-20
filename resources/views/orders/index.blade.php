@extends('layouts.store')

@section('title', 'Lịch sử đơn hàng | Việt Thịnh')

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.show') }}">Tài khoản</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lịch sử đơn hàng</li>
            </ol>
        </nav>

        <div class="mb-4">
            <h1 class="h4 fw-bold text-dark mb-1">Lịch sử đơn hàng</h1>
            <p class="text-muted small mb-0">Theo dõi trạng thái và chi tiết các đơn hàng bạn đã mua sắm tại Việt Thịnh</p>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>

            <div class="col-12 col-lg-9">
                @if ($orders->isNotEmpty())
                    <div class="card border-0 shadow-sm rounded-3 p-4">
                        <div class="d-flex flex-column gap-3">
                            @foreach ($orders as $order)
                                @php
                                    $statusClass = match($order->order_status) {
                                        'COMPLETED' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25',
                                        'CANCELLED' => 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25',
                                        'PROCESSING' => 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25',
                                        default => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25',
                                    };
                                @endphp
                                <div class="p-3 rounded-3 border bg-white transition hover-shadow">
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark fs-6">Đơn hàng #{{ $order->order_code }}</span>
                                            <span class="badge border {{ $statusClass }}" style="font-size: 0.72rem;">
                                                {{ $order->order_status }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $order->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top flex-wrap gap-2">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 0.78rem;">
                                                Thanh toán: <span class="fw-semibold text-dark">{{ $order->payment_method }} ({{ $order->payment_status }})</span>
                                            </small>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-end">
                                                <small class="text-muted d-block" style="font-size: 0.75rem;">Tổng tiền</small>
                                                <strong class="text-danger fs-6">{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</strong>
                                            </div>
                                            <a class="btn btn-outline-primary btn-sm px-3" href="{{ route('orders.show', $order) }}">
                                                Chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="store-pagination mt-4">{{ $orders->links() }}</div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
                        <i class="bi bi-receipt fs-1 text-muted d-block mb-2"></i>
                        <h5 class="fw-bold text-dark">Bạn chưa có đơn hàng nào</h5>
                        <p class="text-muted small mb-3">Hãy dạo quanh cửa hàng và chọn những món đồ văn phòng phẩm ưng ý nhé.</p>
                        <div>
                            <a class="btn btn-primary px-4 fw-semibold" href="{{ route('products.index') }}">Khám phá sản phẩm</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
