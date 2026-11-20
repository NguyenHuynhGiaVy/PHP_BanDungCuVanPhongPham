@extends('layouts.admin')

@section('title', 'Quản lý thương hiệu | Stationery Store')
@section('page-heading', 'Thương hiệu')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span><h1>Thương hiệu</h1><p>Quản lý thương hiệu đang được sử dụng trong cửa hàng.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.brands.create') }}"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm thương hiệu</a>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách thương hiệu</h2><p>{{ $brands->total() }} thương hiệu</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="brand-search">Tìm thương hiệu</label>
                <input class="form-control form-control-sm" id="brand-search" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên">
                <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" title="Tìm kiếm" aria-label="Tìm kiếm"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Thương hiệu</th><th>Sản phẩm</th><th>Trạng thái</th><th class="text-center">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td><strong>{{ $brand->name }}</strong></td>
                            <td>{{ $brand->products_count }}</td>
                            <td><span class="badge {{ $brand->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $brand->status ? 'Đang hoạt động' : 'Đã ẩn' }}</span></td>
                            <td class="text-center"><div class="admin-row-actions justify-content-center">
                                <a class="btn btn-outline-primary btn-sm admin-icon-button" href="{{ route('admin.brands.edit', $brand) }}" title="Sửa thương hiệu" aria-label="Sửa thương hiệu"><i class="bi bi-pencil-square"></i></a>
                                @if ($brand->products_count === 0)
                                    <form id="delete-brand-{{ $brand->id }}" action="{{ route('admin.brands.destroy', $brand) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm admin-icon-button" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-confirm data-admin-form="delete-brand-{{ $brand->id }}" data-admin-message="Xóa thương hiệu {{ $brand->name }}?" title="Xóa thương hiệu" aria-label="Xóa thương hiệu"><i class="bi bi-trash3"></i></button>
                                    </form>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="admin-empty-note">Chưa có thương hiệu phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $brands->links() }}</div>
    </div>
@endsection
