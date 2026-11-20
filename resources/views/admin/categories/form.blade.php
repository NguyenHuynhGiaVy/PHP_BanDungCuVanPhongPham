@extends('layouts.admin')

@php($isEditing = $category->exists)

@section('title', ($isEditing ? 'Sửa danh mục' : 'Thêm danh mục').' | Stationery Store')
@section('page-heading', 'Danh mục')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span>
            <h1>{{ $isEditing ? 'Sửa danh mục' : 'Thêm danh mục' }}</h1>
            <p>Thông tin danh mục sản phẩm.</p>
        </div>
        <a class="btn btn-outline-secondary admin-icon-button" href="{{ route('admin.categories.index') }}" title="Quay lại danh sách" aria-label="Quay lại danh sách"><i class="bi bi-arrow-left"></i></a>
    </div>
    <section class="admin-panel admin-form-panel">
        <form action="{{ $isEditing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="category-name">Tên danh mục</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="category-name" name="name" value="{{ old('name', $category->name) }}" maxlength="150" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="category-slug">Slug</label>
                    <input class="form-control @error('slug') is-invalid @enderror" id="category-slug" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="180" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="category-parent">Danh mục cha</label>
                    <select class="form-select" id="category-parent" name="parent_id">
                        <option value="">Không có</option>
                        @foreach ($parentCategories as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $parent->id)>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="category-status">Trạng thái</label>
                    <select class="form-select" id="category-status" name="status" required>
                        <option value="1" @selected((string) old('status', $category->status) === '1')>Đang hoạt động</option>
                        <option value="0" @selected((string) old('status', $category->status) === '0')>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="category-description">Mô tả</label>
                    <textarea class="form-control" id="category-description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>
                <div class="col-12 d-flex justify-content-end pt-3 border-top"><button class="btn btn-primary admin-save-button" type="submit"><i class="bi bi-check2-circle me-1"></i>{{ $isEditing ? 'Lưu thay đổi' : 'Tạo danh mục' }}</button></div>
            </div>
        </form>
    </section>
@endsection
