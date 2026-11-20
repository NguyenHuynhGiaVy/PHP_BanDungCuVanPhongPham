@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng | Stationery Store')
@section('page-heading', 'Đơn hàng')

@section('content')
    <div class="admin-page-heading"><div><span class="admin-eyebrow">VẬN HÀNH</span><h1>Đơn hàng</h1><p>Theo dõi giao dịch và trạng thái xử lý.</p></div></div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách đơn hàng</h2><p>{{ $orders->total() }} đơn hàng</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="order-search">Tìm đơn hàng</label>
                <input class="form-control form-control-sm" id="order-search" name="q" value="{{ request('q') }}" placeholder="Mã đơn, người nhận, điện thoại">
                <select class="form-select form-select-sm" name="order_status" aria-label="Lọc trạng thái">
                    <option value="">Mọi trạng thái</option>
                    @foreach (['PENDING', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED'] as $status)
                        <option value="{{ $status }}" @selected(request('order_status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" title="Lọc dữ liệu" aria-label="Lọc dữ liệu"><i class="bi bi-funnel"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Người nhận</th><th>Tổng tiền</th><th>Tiến trình</th><th class="text-center">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><strong>{{ $order->order_code }}</strong><small class="d-block text-secondary">{{ $order->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>{{ $order->user->full_name }}</td>
                            <td>{{ $order->receiver_name }}<small class="d-block text-secondary">{{ $order->receiver_phone }}</small></td>
                            <td>{{ number_format((float) $order->total_amount) }} ₫</td>
                            @php
                                $orderStatusMeta = match ($order->order_status) {
                                    'PENDING' => ['Chờ xác nhận', 'text-bg-warning'],
                                    'PROCESSING' => ['Đang xử lý', 'text-bg-primary'],
                                    'SHIPPED' => ['Đang giao', 'text-bg-info'],
                                    'DELIVERED' => ['Đã giao', 'text-bg-success'],
                                    'CANCELLED' => ['Đã hủy', 'text-bg-danger'],
                                    default => [$order->order_status, 'text-bg-secondary'],
                                };
                            @endphp
                            <td><span class="badge {{ $orderStatusMeta[1] }}">{{ $orderStatusMeta[0] }}</span></td>
                            <td class="text-center"><a class="btn btn-outline-primary btn-sm admin-icon-button" href="{{ route('admin.orders.show', $order) }}" title="Xem chi tiết đơn hàng" aria-label="Xem chi tiết đơn hàng"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="admin-empty-note">Chưa có đơn hàng phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $orders->links() }}</div>
    </div>
@endsection
