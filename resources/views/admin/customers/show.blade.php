@extends('layouts.admin')

@section('title', 'Chi tiết khách hàng | Stationery Store')
@section('page-heading', 'Khách hàng')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">TÀI KHOẢN KHÁCH HÀNG</span><h1>{{ $customer->full_name }}</h1><p>{{ $customer->email }}</p></div>
        <a class="btn btn-outline-secondary admin-icon-button" href="{{ route('admin.customers.index') }}" title="Quay lại danh sách" aria-label="Quay lại danh sách"><i class="bi bi-arrow-left"></i></a>
    </div>
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <section class="admin-panel">
                <div class="admin-panel-heading"><div><h2>Thông tin tài khoản</h2></div></div>
                <dl class="admin-details-list">
                    <dt>Email</dt><dd>{{ $customer->email }}</dd>
                    <dt>Điện thoại</dt><dd>{{ $customer->phone ?: '—' }}</dd>
                    <dt>Ngày tạo</dt><dd>{{ $customer->created_at->format('d/m/Y H:i') }}</dd>
                    <dt>Vai trò</dt><dd>{{ $customer->role->name }}</dd>
                    <dt>Trạng thái</dt><dd>{{ $customer->status ? 'Đang hoạt động' : 'Đã khóa' }}</dd>
                </dl>
                <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="admin-status-form">
                    @csrf @method('PATCH')
                    <label class="form-label" for="customer-status">Trạng thái tài khoản</label>
                    <select class="form-select mb-2" id="customer-status" name="status" required>
                        <option value="1" @selected((int) old('status', $customer->status) === 1)>Đang hoạt động</option>
                        <option value="0" @selected((int) old('status', $customer->status) === 0)>Đã khóa</option>
                    </select>
                    <button class="btn btn-primary w-100" type="submit">Lưu trạng thái</button>
                </form>
            </section>
        </div>
        <div class="col-12 col-xl-8">
            <section class="admin-panel">
                <div class="admin-panel-heading"><div><h2>Lịch sử đơn hàng</h2><p>{{ $orders->total() }} đơn hàng</p></div></div>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr><td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_code }}</a></td><td>{{ $order->created_at->format('d/m/Y') }}</td><td>{{ number_format((float) $order->total_amount) }} ₫</td><td>{{ $order->order_status }}</td></tr>
                            @empty
                                <tr><td colspan="4"><div class="admin-empty-note">Khách hàng chưa có đơn hàng.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="admin-pagination">{{ $orders->links() }}</div>
            </section>
        </div>
    </div>
@endsection
