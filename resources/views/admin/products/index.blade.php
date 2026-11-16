@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm | Stationery Store')
@section('page-heading', 'Sản phẩm')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span><h1>Sản phẩm</h1><p>Quản lý nội dung sản phẩm và biến thể đầu tiên.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.products.create') }}"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm sản phẩm</a>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách sản phẩm</h2><p>{{ $products->total() }} sản phẩm</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="product-search">Tìm sản phẩm</label>
                <input class="form-control form-control-sm" id="product-search" name="q" value="{{ request('q') }}" placeholder="Tên hoặc slug">
                <select class="form-select form-select-sm" name="status" aria-label="Lọc trạng thái">
                    <option value="">Mọi trạng thái</option>
                    <option value="1" @selected(request('status') === '1')>Đang hoạt động</option>
                    <option value="0" @selected(request('status') === '0')>Đã ẩn</option>
                </select>
                <button class="btn btn-outline-primary btn-sm" type="submit">Lọc</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Sản phẩm</th><th>Danh mục</th><th>Thương hiệu</th><th>Biến thể</th><th>Trạng thái</th><th class="text-end">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong><small class="d-block text-secondary">{{ $product->slug }}</small></td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->brand?->name ?? '—' }}</td>
                            <td>{{ $product->variants_count }}</td>
                            <td><span class="badge {{ $product->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $product->status ? 'Đang bán' : 'Đã ẩn' }}</span></td>
                            <td class="text-end">
                                <div class="admin-row-actions">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.products.edit', $product) }}">Sửa</a>
                                    @if ($product->variants_count === 0 && $product->reviews_count === 0 && $product->wishlists_count === 0)
                                        <form id="delete-product-{{ $product->id }}" action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-form="delete-product-{{ $product->id }}" data-admin-message="Xóa sản phẩm {{ $product->name }}?">Xóa</button>
                                        </form>
                                    @else
                                        <span class="small text-secondary">Ẩn để lưu lịch sử</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="admin-empty-note">Chưa có sản phẩm phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $products->links() }}</div>
    </div>
@endsection
