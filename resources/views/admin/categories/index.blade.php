@extends('layouts.admin')

@section('title', 'Quản lý danh mục | Stationery Store')
@section('page-heading', 'Danh mục')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span>
            <h1>Danh mục</h1>
            <p>Quản lý nhóm sản phẩm đang hiển thị trên cửa hàng.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.categories.create') }}"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm danh mục</a>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách danh mục</h2><p>{{ $categories->total() }} danh mục</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="category-search">Tìm danh mục</label>
                <input class="form-control form-control-sm" id="category-search" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên">
                <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" title="Tìm kiếm" aria-label="Tìm kiếm"><i class="bi bi-search"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Tên danh mục</th><th>Sản phẩm</th><th>Trạng thái</th><th class="text-center">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td><span class="badge {{ $category->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->status ? 'Đang hoạt động' : 'Đã ẩn' }}</span></td>
                            <td class="text-center">
                                <div class="admin-row-actions justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm admin-icon-button" href="{{ route('admin.categories.edit', $category) }}" title="Sửa danh mục" aria-label="Sửa danh mục"><i class="bi bi-pencil-square"></i></a>
                                    @if ($category->products_count === 0 && $category->children_count === 0)
                                        <form id="delete-category-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm admin-icon-button" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-confirm data-admin-form="delete-category-{{ $category->id }}" data-admin-message="Xóa danh mục {{ $category->name }}?" title="Xóa danh mục" aria-label="Xóa danh mục"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="admin-empty-note">Chưa có danh mục phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $categories->links() }}</div>
    </div>
@endsection
