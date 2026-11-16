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
                <button class="btn btn-outline-primary btn-sm" type="submit">Tìm</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Thương hiệu</th><th>Slug</th><th>Sản phẩm</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td><span class="admin-brand-cell">@if($brand->logo)<img src="{{ asset($brand->logo) }}" alt="" loading="lazy">@else<i class="bi bi-award" aria-hidden="true"></i>@endif<strong>{{ $brand->name }}</strong></span></td>
                            <td><code>{{ $brand->slug }}</code></td>
                            <td>{{ $brand->products_count }}</td>
                            <td><span class="badge {{ $brand->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $brand->status ? 'Đang hoạt động' : 'Đã ẩn' }}</span></td>
                            <td class="text-end"><div class="admin-row-actions">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.brands.edit', $brand) }}">Sửa</a>
                                @if ($brand->products_count === 0)
                                    <form id="delete-brand-{{ $brand->id }}" action="{{ route('admin.brands.destroy', $brand) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-confirm data-admin-form="delete-brand-{{ $brand->id }}" data-admin-message="Xóa thương hiệu {{ $brand->name }}?">Xóa</button>
                                    </form>
                                @else
                                    <span class="small text-secondary">Ẩn để lưu dữ liệu</span>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="admin-empty-note">Chưa có thương hiệu phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $brands->links() }}</div>
    </div>
@endsection
