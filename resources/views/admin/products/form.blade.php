@extends('layouts.admin')

@php
    $isEditing = $product->exists;
    $variantInputs = old('variants', [[
        'sku' => '',
        'variant_name' => 'Mẫu tiêu chuẩn',
        'attributes' => [['name' => '', 'value' => '']],
        'price' => 0,
        'compare_price' => '',
    ]]);
    $variantInputs = is_array($variantInputs)
        ? array_filter($variantInputs, 'is_array')
        : [];
    $variantInputs = $variantInputs !== [] ? $variantInputs : [[
        'sku' => '',
        'variant_name' => 'Mẫu tiêu chuẩn',
        'attributes' => [['name' => '', 'value' => '']],
        'price' => 0,
        'compare_price' => '',
    ]];
    $nextVariantIndex = max(array_map('intval', array_keys($variantInputs))) + 1;
    $primaryAttributeRows = old(
        'attributes',
        collect($primaryVariant->attributes ?? [])
            ->map(fn ($value, $name): array => ['name' => $name, 'value' => $value])
            ->values()
            ->all(),
    );
    $primaryAttributeRows = is_array($primaryAttributeRows) ? $primaryAttributeRows : [];
@endphp

@section('title', ($isEditing ? 'Sửa sản phẩm' : 'Thêm sản phẩm').' | Stationery Store')
@section('page-heading', 'Sản phẩm')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span><h1>{{ $isEditing ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h1><p>Thông tin cơ bản và các biến thể của sản phẩm.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Quay lại</a>
    </div>
    <section class="admin-panel admin-form-panel">
        <form id="product-form" action="{{ $isEditing ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div class="admin-form-section">
                <h2>Thông tin sản phẩm</h2>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="product-name">Tên sản phẩm</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="product-name" name="name" value="{{ old('name', $product->name) }}" maxlength="200" data-product-name @if (! $isEditing) data-auto-slug-name @endif required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="product-slug">Slug</label>
                        <input class="form-control @error('slug') is-invalid @enderror" id="product-slug" name="slug" value="{{ old('slug', $product->slug) }}" maxlength="220" @if (! $isEditing) data-auto-slug-output @endif required>
                        @unless ($isEditing)
                            <input type="hidden" name="slug_is_auto" value="{{ old('slug_is_auto', '1') }}" data-slug-auto-flag>
                            <div class="form-text">Slug tự tạo theo tên sản phẩm; chỉnh sửa ô này để dùng slug riêng.</div>
                        @endunless
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="product-category">Danh mục</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="product-category" name="category_id" required>
                            <option value="">Chọn danh mục</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="product-brand">Thương hiệu</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror" id="product-brand" name="brand_id">
                            <option value="">Chưa gắn thương hiệu</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected((string) old('brand_id', $product->brand_id) === (string) $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="product-description">Mô tả</label>
                        <textarea class="form-control" id="product-description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="product-status">Trạng thái</label>
                        <select class="form-select" id="product-status" name="status" required>
                            <option value="1" @selected((string) old('status', $product->status) === '1')>Đang bán</option>
                            <option value="0" @selected((string) old('status', $product->status) === '0')>Đã ẩn</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="admin-form-section">
                @if ($isEditing)
                    <h2>Biến thể đầu tiên</h2>
                    @if ($product->variants->count() > 1)
                        <div class="alert alert-info small">Sản phẩm còn {{ $product->variants->count() - 1 }} biến thể khác. Chỉnh sửa kho tại mục Tồn kho; biến thể bổ sung giữ nguyên dữ liệu.</div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="product-sku">SKU</label>
                            <input class="form-control @error('sku') is-invalid @enderror" id="product-sku" name="sku" value="{{ old('sku', $primaryVariant->sku) }}" maxlength="80" required>
                            @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="product-variant-name">Tên phân loại</label>
                            <input class="form-control" id="product-variant-name" name="variant_name" value="{{ old('variant_name', $primaryVariant->variant_name ?: 'Mẫu tiêu chuẩn') }}" maxlength="150" required>
                        </div>
                        <div class="col-12">
                            @include('admin.products.partials.attribute-fields', [
                                'attributeId' => 'product-primary-attribute',
                                'attributePrefix' => 'attributes',
                                'attributeRows' => $primaryAttributeRows,
                            ])
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="product-price">Giá bán (₫)</label>
                            <input class="form-control @error('price') is-invalid @enderror" id="product-price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $primaryVariant->price) }}" required>
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label" for="product-compare-price">Giá so sánh (₫)</label>
                            <input class="form-control @error('compare_price') is-invalid @enderror" id="product-compare-price" name="compare_price" type="number" min="0" step="0.01" value="{{ old('compare_price', $primaryVariant->compare_price) }}">
                            @error('compare_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12"><a href="{{ route('admin.inventory.index') }}">Chỉnh sửa số lượng tại mục Tồn kho.</a></div>
                    </div>
                @else
                    <div class="admin-panel-heading">
                        <div><h2>Biến thể</h2><p>Thêm một hoặc nhiều phân loại, mỗi biến thể sẽ có tồn kho ban đầu là 0.</p></div>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-add-variant>Thêm biến thể</button>
                    </div>
                    @error('variants')<div class="alert alert-danger">{{ $message }}</div>@enderror
                    <div data-variant-list data-next-index="{{ $nextVariantIndex }}">
                        @foreach ($variantInputs as $variantIndex => $variantInput)
                            @include('admin.products.partials.variant-fields', [
                                'variantIndex' => $variantIndex,
                                'variantInput' => $variantInput,
                            ])
                        @endforeach
                    </div>
                    <template data-variant-template>
                        @include('admin.products.partials.variant-fields', [
                            'variantIndex' => '__VARIANT_INDEX__',
                            'variantInput' => [
                                'sku' => '',
                                'variant_name' => 'Mẫu tiêu chuẩn',
                                'attributes' => [['name' => '', 'value' => '']],
                                'price' => 0,
                                'compare_price' => '',
                            ],
                        ])
                    </template>
                @endif
            </div>

            <div class="admin-form-section">
                <h2>Ảnh sản phẩm</h2>
                @if ($isEditing && $product->images->isNotEmpty())
                    <div class="admin-image-strip mb-3">
                        @foreach ($product->images as $image)
                            <div>
                                <img src="{{ asset($image->image_url) }}" alt="" loading="lazy">
                                <small>{{ $image->is_primary ? 'Ảnh chính' : 'Ảnh phụ' }}</small>
                                <div class="admin-image-actions">
                                    @unless ($image->is_primary)
                                        <button class="btn btn-outline-primary btn-sm" type="submit" form="set-primary-image-{{ $image->id }}">Đặt ảnh chính</button>
                                    @endunless
                                    <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-form="delete-image-{{ $image->id }}" data-admin-message="Xóa ảnh {{ $image->image_url }}?">Xóa ảnh</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <label class="form-label" for="product-image-url">{{ $isEditing ? 'Thêm ảnh mới' : 'Đường dẫn ảnh' }} (không bắt buộc)</label>
                <input class="form-control @error('image_url') is-invalid @enderror" id="product-image-url" name="image_url" value="{{ old('image_url') }}" maxlength="255">
                @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Nhập đường dẫn ảnh trong thư mục public. Không tải ảnh lên máy chủ.</div>
            </div>

            @if ($isEditing)
                <div class="admin-form-section">
                    <div class="admin-panel-heading">
                        <div><h2>Biến thể và tồn kho</h2><p>{{ $product->variants->count() }} biến thể</p></div>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.products.variants.create', $product) }}">Thêm biến thể</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table admin-table align-middle mb-0">
                            <thead><tr><th>SKU / phân loại</th><th>Giá</th><th>Tồn / khả dụng</th><th>Trạng thái</th><th></th></tr></thead>
                            <tbody>
                                @forelse ($product->variants as $variant)
                                    <tr>
                                        <td><strong>{{ $variant->sku }}</strong><small class="d-block text-secondary">{{ $variant->variant_name }}</small></td>
                                        <td>{{ number_format((float) $variant->price) }} ₫</td>
                                        <td>{{ number_format($variant->inventory?->quantity ?? 0) }} / {{ number_format(max(0, ($variant->inventory?->quantity ?? 0) - ($variant->inventory?->reserved_quantity ?? 0))) }}</td>
                                        <td><span class="badge {{ $variant->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $variant->status ? 'Đang bán' : 'Đã ẩn' }}</span></td>
                                        <td>
                                            <div class="admin-row-actions">
                                                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.products.variants.edit', [$product, $variant]) }}">Sửa</a>
                                                <button class="btn btn-outline-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-form="delete-variant-{{ $variant->id }}" data-admin-message="Xóa hoặc ẩn biến thể {{ $variant->sku }}? Nếu có lịch sử, biến thể sẽ được ẩn.">Xóa / ẩn</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5"><div class="admin-empty-note">Chưa có biến thể.</div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="form-text mt-2 mb-0">Điều chỉnh số lượng tại mục <a href="{{ route('admin.inventory.index') }}">Tồn kho</a>; mọi thay đổi đều có sổ giao dịch.</p>
                </div>
            @endif

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit" form="product-form">{{ $isEditing ? 'Lưu thay đổi' : 'Tạo sản phẩm' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Hủy</a>
            </div>
        </form>
        @if ($isEditing)
            @foreach ($product->images as $image)
                @unless ($image->is_primary)
                    <form id="set-primary-image-{{ $image->id }}" class="d-none" action="{{ route('admin.products.images.primary', [$product, $image]) }}" method="POST">
                        @csrf @method('PATCH')
                    </form>
                @endunless
                <form id="delete-image-{{ $image->id }}" class="d-none" action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            @foreach ($product->variants as $variant)
                <form id="delete-variant-{{ $variant->id }}" class="d-none" action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        @endif
    </section>
@endsection
