@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng | Stationery Store')
@section('page-heading', 'Đơn hàng')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">ĐƠN {{ $order->order_code }}</span><h1>Chi tiết đơn hàng</h1><p>Tạo lúc {{ $order->created_at->format('d/m/Y H:i') }}</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.orders.index') }}">Quay lại danh sách</a>
    </div>
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <section class="admin-panel mb-4">
                <div class="admin-panel-heading"><div><h2>Sản phẩm đặt mua</h2><p>{{ $order->items->count() }} dòng sản phẩm</p></div></div>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Sản phẩm</th><th>SKU</th><th>Đơn giá</th><th>Số lượng</th><th>Tạm tính</th></tr></thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr><td><strong>{{ $item->product_name }}</strong><small class="d-block text-secondary">{{ $item->variant_name }}</small></td><td>{{ $item->sku }}</td><td>{{ number_format((float) $item->price) }} ₫</td><td>{{ $item->quantity }}</td><td>{{ number_format((float) $item->subtotal) }} ₫</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="admin-order-total">
                    <span>Tạm tính <strong>{{ number_format((float) $order->subtotal) }} ₫</strong></span>
                    <span>Giảm giá <strong>-{{ number_format((float) $order->discount_amount) }} ₫</strong></span>
                    <span>Phí giao hàng <strong>{{ number_format((float) $order->shipping_fee) }} ₫</strong></span>
                    <span class="admin-order-grand-total">Tổng cộng <strong>{{ number_format((float) $order->total_amount) }} ₫</strong></span>
                </div>
            </section>
            <section class="admin-panel">
                <div class="admin-panel-heading"><div><h2>Thông tin thanh toán</h2><p>Phương thức {{ $order->payment_method }} · {{ $order->payment_status }}</p></div></div>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Mã giao dịch</th><th>Phương thức</th><th>Số tiền</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
                        <tbody>
                            @forelse ($order->payments as $payment)
                                <tr><td>{{ $payment->transaction_code ?: '—' }}</td><td>{{ $payment->payment_method }}</td><td>{{ number_format((float) $payment->amount) }} ₫</td><td>{{ $payment->status }}</td><td>{{ $payment->paid_at?->format('d/m/Y H:i') ?: '—' }}</td></tr>
                            @empty
                                <tr><td colspan="5">Chưa có giao dịch thanh toán.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-12 col-xl-4">
            <section class="admin-panel mb-4">
                <div class="admin-panel-heading"><div><h2>Người nhận</h2></div></div>
                <dl class="admin-details-list">
                    <dt>Khách hàng</dt><dd>{{ $order->user->full_name }}<small class="d-block text-secondary">{{ $order->user->email }}</small></dd>
                    <dt>Người nhận</dt><dd>{{ $order->receiver_name }}</dd>
                    <dt>Điện thoại</dt><dd>{{ $order->receiver_phone }}</dd>
                    <dt>Địa chỉ</dt><dd>{{ $order->shipping_address }}</dd>
                    <dt>Ghi chú</dt><dd>{{ $order->note ?: '—' }}</dd>
                    <dt>Trạng thái</dt><dd>{{ $order->order_status }}</dd>
                </dl>
                @if ($nextStatuses !== [])
                    <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="admin-status-form">
                        @csrf @method('PATCH')
                        <label class="form-label" for="order-status">Chuyển trạng thái</label>
                        <select class="form-select mb-2" id="order-status" name="order_status" required>
                            @foreach ($nextStatuses as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach
                        </select>
                        <button class="btn btn-primary w-100" type="submit">Cập nhật trạng thái</button>
                    </form>
                @else
                    <p class="small text-secondary mb-0">Đơn hàng không có bước xử lý tiếp theo trong luồng hiện tại.</p>
                @endif
            </section>
        </div>
    </div>
@endsection
