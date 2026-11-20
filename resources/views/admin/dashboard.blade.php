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
        <a class="btn btn-success" href="{{ route('admin.dashboard.export-excel') }}">
            <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>Xuất Excel
        </a>
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

    <div class="row g-4 mb-4">
        <section class="col-12 col-xl-8">
            <div class="admin-panel h-100">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Doanh thu 6 tháng gần nhất</h2>
                        <p>Doanh thu đơn hàng đã thanh toán</p>
                    </div>
                </div>
                <div class="admin-chart-wrap">
                    <canvas id="admin-revenue-chart" height="260" aria-label="Biểu đồ doanh thu"></canvas>
                </div>
            </div>
        </section>
        <aside class="col-12 col-xl-4">
            <div class="admin-panel h-100">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Trạng thái đơn hàng</h2>
                        <p>Tỷ lệ theo trạng thái hiện tại</p>
                    </div>
                </div>
                <div class="admin-chart-wrap admin-chart-donut-wrap">
                    <canvas id="admin-orders-chart" height="220" aria-label="Biểu đồ trạng thái đơn hàng"></canvas>
                </div>
                <div class="admin-chart-legend">
                    @foreach ($orderStatusCounts as $status => $count)
                        <span><i class="admin-status-dot admin-status-dot-{{ strtolower($status) }}"></i>{{ $status }}: <strong>{{ $count }}</strong></span>
                    @endforeach
                </div>
            </div>
        </aside>
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

    @php
        $chartLabels = $monthlyRevenue->pluck('label')->values();
        $chartRevenue = $monthlyRevenue->pluck('revenue')->values();
        $chartOrders = $monthlyRevenue->pluck('orders')->values();
        $chartStatuses = $orderStatusCounts->values();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const labels = @json($chartLabels);
            const revenue = @json($chartRevenue);
            const orders = @json($chartOrders);
            const statusValues = @json($chartStatuses);
            const statusColors = ['#f59e0b', '#38bdf8', '#6366f1', '#22c55e', '#ef4444'];

            const revenueCanvas = document.getElementById('admin-revenue-chart');
            if (revenueCanvas) {
                const ctx = revenueCanvas.getContext('2d');
                const width = revenueCanvas.parentElement.clientWidth;
                const height = 260;
                const scale = window.devicePixelRatio || 1;
                revenueCanvas.width = width * scale;
                revenueCanvas.height = height * scale;
                revenueCanvas.style.width = width + 'px';
                revenueCanvas.style.height = height + 'px';
                ctx.scale(scale, scale);

                const padding = { top: 20, right: 20, bottom: 38, left: 68 };
                const chartWidth = width - padding.left - padding.right;
                const chartHeight = height - padding.top - padding.bottom;
                const maxRevenue = Math.max(...revenue, 1);
                const gridCount = 4;

                ctx.font = '12px Be Vietnam Pro, sans-serif';
                ctx.textAlign = 'right';
                ctx.textBaseline = 'middle';
                ctx.strokeStyle = '#e5e7eb';
                ctx.fillStyle = '#6b7280';
                ctx.lineWidth = 1;

                for (let i = 0; i <= gridCount; i++) {
                    const y = padding.top + (chartHeight / gridCount) * i;
                    const value = maxRevenue - (maxRevenue / gridCount) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();
                    ctx.fillText(Math.round(value / 1000).toLocaleString('vi-VN') + 'k ₫', padding.left - 8, y);
                }

                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                ctx.fillStyle = '#374151';
                const step = chartWidth / labels.length;
                labels.forEach((label, index) => {
                    const x = padding.left + step * index + step / 2;
                    ctx.fillText(label, x, height - 28);

                    const barHeight = (revenue[index] / maxRevenue) * chartHeight;
                    const barWidth = Math.min(48, step * 0.48);
                    const barX = x - barWidth / 2;
                    const barY = padding.top + chartHeight - barHeight;
                    ctx.fillStyle = '#0b5cad';
                    ctx.beginPath();
                    ctx.roundRect(barX, barY, barWidth, barHeight, 6);
                    ctx.fill();
                });
            }

            const ordersCanvas = document.getElementById('admin-orders-chart');
            if (ordersCanvas) {
                const ctx = ordersCanvas.getContext('2d');
                const width = ordersCanvas.parentElement.clientWidth;
                const height = 220;
                const scale = window.devicePixelRatio || 1;
                ordersCanvas.width = width * scale;
                ordersCanvas.height = height * scale;
                ordersCanvas.style.width = width + 'px';
                ordersCanvas.style.height = height + 'px';
                ctx.scale(scale, scale);

                const total = statusValues.reduce((sum, value) => sum + Number(value), 0);
                if (total === 0) {
                    ctx.fillStyle = '#9ca3af';
                    ctx.textAlign = 'center';
                    ctx.fillText('Chưa có đơn hàng', width / 2, height / 2);
                    return;
                }

                const centerX = width / 2;
                const centerY = height / 2;
                const radius = Math.min(width, height) * 0.34;
                let start = -Math.PI / 2;
                statusValues.forEach((value, index) => {
                    const slice = (Number(value) / total) * Math.PI * 2;
                    ctx.beginPath();
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, start, start + slice);
                    ctx.closePath();
                    ctx.fillStyle = statusColors[index];
                    ctx.fill();
                    start += slice;
                });
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius * 0.58, 0, Math.PI * 2);
                ctx.fillStyle = '#fff';
                ctx.fill();
                ctx.fillStyle = '#111827';
                ctx.font = '700 22px Be Vietnam Pro, sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(total, centerX, centerY - 8);
                ctx.fillStyle = '#6b7280';
                ctx.font = '12px Be Vietnam Pro, sans-serif';
                ctx.fillText('đơn hàng', centerX, centerY + 14);
            }
        });
    </script>
@endsection
