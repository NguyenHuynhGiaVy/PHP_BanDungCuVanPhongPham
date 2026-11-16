@php
    $attributeRows = is_array($attributeRows ?? null) ? array_values(array_filter($attributeRows, 'is_array')) : [];
    $attributeRows = $attributeRows !== [] ? $attributeRows : [['name' => '', 'value' => '']];
    $nextAttributeIndex = count($attributeRows);
@endphp

<div data-attribute-fields>
    <div class="mb-2"><strong>Thuộc tính biến thể</strong><span class="form-text d-block">Nhập tên thuộc tính và giá trị riêng cho biến thể này.</span></div>
    <div data-attribute-list data-next-index="{{ $nextAttributeIndex }}">
        @foreach ($attributeRows as $attributeIndex => $attribute)
            <div class="row g-2 align-items-end mb-2" data-attribute-row>
                <div class="col-5">
                    <label class="form-label" for="{{ $attributeId }}-{{ $attributeIndex }}-name">Tên thuộc tính</label>
                    <input class="form-control" id="{{ $attributeId }}-{{ $attributeIndex }}-name" name="{{ $attributePrefix }}[{{ $attributeIndex }}][name]" value="{{ $attribute['name'] ?? '' }}" maxlength="80" placeholder="Ví dụ: Màu sắc">
                </div>
                <div class="col-5">
                    <label class="form-label" for="{{ $attributeId }}-{{ $attributeIndex }}-value">Giá trị</label>
                    <input class="form-control" id="{{ $attributeId }}-{{ $attributeIndex }}-value" name="{{ $attributePrefix }}[{{ $attributeIndex }}][value]" value="{{ $attribute['value'] ?? '' }}" maxlength="150" placeholder="Ví dụ: Xanh">
                </div>
                <div class="col-2">
                    <button class="btn btn-outline-danger w-100" type="button" data-remove-attribute aria-label="Xóa thuộc tính">Xóa</button>
                </div>
            </div>
        @endforeach
    </div>
    <template data-attribute-template>
        <div class="row g-2 align-items-end mb-2" data-attribute-row>
            <div class="col-5">
                <label class="form-label" for="{{ $attributeId }}-__ATTRIBUTE_INDEX__-name">Tên thuộc tính</label>
                <input class="form-control" id="{{ $attributeId }}-__ATTRIBUTE_INDEX__-name" name="{{ $attributePrefix }}[__ATTRIBUTE_INDEX__][name]" maxlength="80" placeholder="Ví dụ: Màu sắc">
            </div>
            <div class="col-5">
                <label class="form-label" for="{{ $attributeId }}-__ATTRIBUTE_INDEX__-value">Giá trị</label>
                <input class="form-control" id="{{ $attributeId }}-__ATTRIBUTE_INDEX__-value" name="{{ $attributePrefix }}[__ATTRIBUTE_INDEX__][value]" maxlength="150" placeholder="Ví dụ: Xanh">
            </div>
            <div class="col-2">
                <button class="btn btn-outline-danger w-100" type="button" data-remove-attribute aria-label="Xóa thuộc tính">Xóa</button>
            </div>
        </div>
    </template>
    <button class="btn btn-outline-secondary btn-sm" type="button" data-add-attribute>Thêm thuộc tính</button>
</div>
