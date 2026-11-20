@extends('layouts.admin')

@section('title', 'Quản lý khách hàng | Stationery Store')
@section('page-heading', 'Khách hàng')

@section('content')
    <div class="admin-page-heading"><div><span class="admin-eyebrow">QUẢN LÝ TÀI KHOẢN</span><h1>Khách hàng</h1><p>Tài khoản có lịch sử mua hàng được giữ nguyên, không xóa cứng.</p></div></div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách khách hàng</h2><p>{{ $customers->total() }} khách hàng</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="customer-search">Tìm khách hàng</label>
                <input class="form-control form-control-sm" id="customer-search" name="q" value="{{ request('q') }}" placeholder="Tên, email hoặc điện thoại">
                <select class="form-select form-select-sm" name="status" aria-label="Lọc trạng thái">
                    <option value="">Mọi trạng thái</option>
                    <option value="1" @selected(request('status') === '1')>Đang hoạt động</option>
                    <option value="0" @selected(request('status') === '0')>Đã khóa</option>
                </select>
                <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" title="Lọc dữ liệu" aria-label="Lọc dữ liệu"><i class="bi bi-funnel"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Khách hàng</th><th>Điện thoại</th><th>Đơn hàng</th><th>Trạng thái</th><th></th></tr></thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><strong>{{ $customer->full_name }}</strong><small class="d-block text-secondary">{{ $customer->email }}</small></td>
                            <td>{{ $customer->phone ?: '—' }}</td>
                            <td>{{ $customer->orders_count }}</td>
                            <td><span class="badge {{ $customer->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $customer->status ? 'Đang hoạt động' : 'Đã khóa' }}</span></td>
                            <td class="text-center"><a class="btn btn-outline-primary btn-sm admin-icon-button" href="{{ route('admin.customers.show', $customer) }}" title="Xem chi tiết khách hàng" aria-label="Xem chi tiết khách hàng"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="admin-empty-note">Chưa có khách hàng phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $customers->links() }}</div>
    </div>
@endsection
