<section class="admin-panel p-3 mb-3" data-variant-card>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="h5 mb-0">Biến thể</h3>
        <button class="btn btn-outline-danger btn-sm" type="button" data-remove-variant>Xóa biến thể</button>
    </div>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label class="form-label" for="product-variant-{{ $variantIndex }}-sku">SKU</label>
            <input class="form-control @error('variants.'.$variantIndex.'.sku') is-invalid @enderror" id="product-variant-{{ $variantIndex }}-sku" name="variants[{{ $variantIndex }}][sku]" value="{{ $variantInput['sku'] ?? '' }}" maxlength="80" required>
            @error('variants.'.$variantIndex.'.sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-6">
            <label class="form-label" for="product-variant-{{ $variantIndex }}-name">Tên phân loại</label>
            <input class="form-control @error('variants.'.$variantIndex.'.variant_name') is-invalid @enderror" id="product-variant-{{ $variantIndex }}-name" name="variants[{{ $variantIndex }}][variant_name]" value="{{ $variantInput['variant_name'] ?? '' }}" maxlength="150" required>
            @error('variants.'.$variantIndex.'.variant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            @include('admin.products.partials.attribute-fields', [
                'attributeId' => 'product-variant-'.$variantIndex.'-attribute',
                'attributePrefix' => 'variants['.$variantIndex.'][attributes]',
                'attributeRows' => $variantInput['attributes'] ?? [],
            ])
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label" for="product-variant-{{ $variantIndex }}-price">Giá bán (₫)</label>
            <input class="form-control @error('variants.'.$variantIndex.'.price') is-invalid @enderror" id="product-variant-{{ $variantIndex }}-price" name="variants[{{ $variantIndex }}][price]" type="number" min="0" step="0.01" value="{{ $variantInput['price'] ?? 0 }}" required>
            @error('variants.'.$variantIndex.'.price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label" for="product-variant-{{ $variantIndex }}-compare-price">Giá so sánh (₫)</label>
            <input class="form-control @error('variants.'.$variantIndex.'.compare_price') is-invalid @enderror" id="product-variant-{{ $variantIndex }}-compare-price" name="variants[{{ $variantIndex }}][compare_price]" type="number" min="0" step="0.01" value="{{ $variantInput['compare_price'] ?? '' }}">
            @error('variants.'.$variantIndex.'.compare_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</section>
