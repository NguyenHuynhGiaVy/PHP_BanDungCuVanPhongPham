@extends('layouts.admin')

@php($isEditing = $brand->exists)

@section('title', ($isEditing ? 'Sửa thương hiệu' : 'Thêm thương hiệu').' | Stationery Store')
@section('page-heading', 'Thương hiệu')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span><h1>{{ $isEditing ? 'Sửa thương hiệu' : 'Thêm thương hiệu' }}</h1><p>Thông tin nhận diện thương hiệu.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.brands.index') }}">Quay lại</a>
    </div>
    <section class="admin-panel admin-form-panel">
        <form action="{{ $isEditing ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" method="POST">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="brand-name">Tên thương hiệu</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="brand-name" name="name" value="{{ old('name', $brand->name) }}" maxlength="150" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="brand-slug">Slug</label>
                    <input class="form-control @error('slug') is-invalid @enderror" id="brand-slug" name="slug" value="{{ old('slug', $brand->slug) }}" maxlength="180" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="brand-logo">Đường dẫn logo (không bắt buộc)</label>
                    <input class="form-control @error('logo') is-invalid @enderror" id="brand-logo" name="logo" value="{{ old('logo', $brand->logo) }}" maxlength="255">
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Nhập đường dẫn ảnh trong thư mục public.</div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="brand-status">Trạng thái</label>
                    <select class="form-select" id="brand-status" name="status" required>
                        <option value="1" @selected((string) old('status', $brand->status) === '1')>Đang hoạt động</option>
                        <option value="0" @selected((string) old('status', $brand->status) === '0')>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-12"><button class="btn btn-primary" type="submit">{{ $isEditing ? 'Lưu thay đổi' : 'Tạo thương hiệu' }}</button></div>
            </div>
        </form>
    </section>
@endsection
