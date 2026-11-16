@extends('layouts.admin')

@php
    $isEditing = $variant->exists;
    $attributeRows = old(
        'attributes',
        collect($variant->attributes ?? [])
            ->map(fn ($value, $name): array => ['name' => $name, 'value' => $value])
            ->values()
            ->all(),
    );
    $attributeRows = is_array($attributeRows) ? $attributeRows : [];
@endphp

@section('title', ($isEditing ? 'Sửa biến thể' : 'Thêm biến thể').' | Stationery Store')
@section('page-heading', 'Sản phẩm')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">BIẾN THỂ SẢN PHẨM</span><h1>{{ $isEditing ? 'Sửa biến thể' : 'Thêm biến thể' }}</h1><p>{{ $product->name }}</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}">Quay lại sản phẩm</a>
    </div>
    <section class="admin-panel admin-form-panel">
        <form action="{{ $isEditing ? route('admin.products.variants.update', [$product, $variant]) : route('admin.products.variants.store', $product) }}" method="POST">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="variant-sku">SKU</label>
                    <input class="form-control @error('sku') is-invalid @enderror" id="variant-sku" name="sku" value="{{ old('sku', $variant->sku) }}" maxlength="80" required>
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="variant-name">Tên phân loại</label>
                    <input class="form-control" id="variant-name" name="variant_name" value="{{ old('variant_name', $variant->variant_name ?: 'Mẫu tiêu chuẩn') }}" maxlength="150" required>
                </div>
                <div class="col-12">
                    @include('admin.products.partials.attribute-fields', [
                        'attributeId' => 'variant-attribute',
                        'attributePrefix' => 'attributes',
                        'attributeRows' => $attributeRows,
                    ])
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="variant-price">Giá bán (₫)</label>
                    <input class="form-control @error('price') is-invalid @enderror" id="variant-price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $variant->price) }}" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="variant-compare-price">Giá so sánh (₫)</label>
                    <input class="form-control @error('compare_price') is-invalid @enderror" id="variant-compare-price" name="compare_price" type="number" min="0" step="0.01" value="{{ old('compare_price', $variant->compare_price) }}">
                    @error('compare_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="variant-status">Trạng thái</label>
                    <select class="form-select" id="variant-status" name="status" required>
                        <option value="1" @selected((string) old('status', $variant->status) === '1')>Đang bán</option>
                        <option value="0" @selected((string) old('status', $variant->status) === '0')>Đã ẩn</option>
                    </select>
                </div>
                @if (! $isEditing)
                    <div class="col-12"><p class="form-text mb-0">Biến thể mới bắt đầu với tồn kho 0. Nhập kho ở mục Tồn kho để ghi nhận lịch sử đầy đủ.</p></div>
                @endif
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ $isEditing ? 'Lưu biến thể' : 'Tạo biến thể' }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}">Hủy</a>
                </div>
            </div>
        </form>
    </section>
@endsection
