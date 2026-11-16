@extends('layouts.admin')

@section('title', 'Dashboard quản trị | Stationery Store')
@section('page-heading', 'Dashboard')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">TỔNG QUAN CỬA HÀNG</span>
            <h1>Dashboard</h1>
            <p>Số liệu được tổng hợp trực tiếp từ dữ liệu cửa hàng.</p>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4">
        @foreach ([
            ['Sản phẩm', $statistics['products'], 'bi-box-seam', 'blue', route('admin.products.index'), false],
            ['Danh mục', $statistics['categories'], 'bi-collection', 'orange', route('admin.categories.index'), false],
            ['Thương hiệu', $statistics['brands'], 'bi-award', 'green', route('admin.brands.index'), false],
            ['Đơn hàng', $statistics['orders'], 'bi-receipt', 'purple', route('admin.orders.index'), false],
            ['Doanh thu đã thanh toán', $statistics['revenue'], 'bi-currency-exchange', 'green', route('admin.orders.index'), true],
        ] as [$label, $value, $icon, $color, $url, $isCurrency])
            <div class="col">
                <a class="admin-stat-card" href="{{ $url }}">
                    <span class="admin-stat-icon admin-stat-{{ $color }}"><i class="bi {{ $icon }}" aria-hidden="true"></i></span>
                    <span class="admin-stat-copy">
                        <small>{{ $label }}</small>
                        <strong>{{ $isCurrency ? number_format((float) $value, 0, ',', '.').' ₫' : number_format($value) }}</strong>
                    </span>
                    <i class="bi bi-arrow-up-right admin-stat-arrow" aria-hidden="true"></i>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <section class="col-12 col-xl-8">
            <div class="admin-panel">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Đơn hàng gần đây</h2>
                        <p>Các đơn hàng mới nhất trong hệ thống</p>
                    </div>
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.orders.index') }}">Tất cả đơn hàng</a>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr><th>Mã đơn</th><th>Khách hàng</th><th>Trạng thái</th><th class="text-end">Tổng tiền</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                <tr>
                                    <td><a class="fw-semibold" href="{{ route('admin.orders.show', $order) }}">{{ $order->order_code }}</a><small class="d-block text-secondary">{{ $order->created_at->format('d/m/Y H:i') }}</small></td>
                                    <td>{{ $order->user?->full_name ?? 'Tài khoản không còn hoạt động' }}</td>
                                    <td><span class="admin-status">{{ $order->order_status }}</span></td>
                                    <td class="text-end fw-semibold">{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</td>
                                </tr>
                            @empty
                                <tr><td class="text-center text-secondary py-4" colspan="4">Chưa có đơn hàng.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <aside class="col-12 col-xl-4">
            <div class="admin-panel h-100">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Cần chú ý</h2>
                        <p>Dữ liệu tồn kho hiện tại</p>
                    </div>
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.inventory.index') }}">Tồn kho</a>
                </div>
                @forelse ($lowStock as $inventory)
                    <div class="admin-stock-row">
                        <span class="admin-stock-icon"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                        <span>
                            <strong>{{ $inventory->variant->product->name }}</strong>
                            <small>{{ $inventory->variant->variant_name }} · {{ $inventory->variant->sku }}</small>
                        </span>
                        <b>{{ $inventory->quantity - $inventory->reserved_quantity }}</b>
                    </div>
                @empty
                    <div class="admin-empty-note">
                        <i class="bi bi-check2-circle" aria-hidden="true"></i>
                        <span>Không có biến thể nào đang hết hàng.</span>
                    </div>
                @endforelse
                <div class="admin-review-notice">
                    <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                    <span><strong>{{ number_format($statistics['pendingReviews']) }}</strong> đánh giá đang chờ duyệt</span>
                    <a href="{{ route('admin.reviews.index') }}">Xem</a>
                </div>
            </div>
        </aside>
    </div>
@endsection
