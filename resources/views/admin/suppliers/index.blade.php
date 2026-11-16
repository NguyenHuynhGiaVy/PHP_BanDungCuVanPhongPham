@extends('layouts.admin')

@section('title', 'Quản lý nhà cung cấp | Stationery Store')
@section('page-heading', 'Nhà cung cấp')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span><h1>Nhà cung cấp</h1><p>Thông tin đối tác cung ứng sản phẩm.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.suppliers.create') }}"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm nhà cung cấp</a>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách nhà cung cấp</h2><p>{{ $suppliers->total() }} nhà cung cấp</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="supplier-search">Tìm nhà cung cấp</label>
                <input class="form-control form-control-sm" id="supplier-search" name="q" value="{{ request('q') }}" placeholder="Tên hoặc email">
                <button class="btn btn-outline-primary btn-sm" type="submit">Tìm</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Nhà cung cấp</th><th>Email / Điện thoại</th><th>Sản phẩm liên kết</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td><strong>{{ $supplier->name }}</strong><small class="d-block text-secondary">{{ $supplier->address ?: 'Chưa có địa chỉ' }}</small></td>
                            <td>{{ $supplier->email ?: '—' }}<small class="d-block text-secondary">{{ $supplier->phone ?: '—' }}</small></td>
                            <td>{{ $supplier->supplier_products_count }}</td>
                            <td><span class="badge {{ $supplier->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $supplier->status ? 'Đang hoạt động' : 'Đã ẩn' }}</span></td>
                            <td class="text-end"><div class="admin-row-actions">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.suppliers.edit', $supplier) }}">Sửa</a>
                                @if ($supplier->supplier_products_count === 0 && $supplier->inventory_transactions_count === 0)
                                    <form id="delete-supplier-{{ $supplier->id }}" action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-confirm data-admin-form="delete-supplier-{{ $supplier->id }}" data-admin-message="Xóa nhà cung cấp {{ $supplier->name }}?">Xóa</button>
                                    </form>
                                @else
                                    <span class="small text-secondary">Ẩn để lưu lịch sử</span>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="admin-empty-note">Chưa có nhà cung cấp phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $suppliers->links() }}</div>
    </div>
@endsection
