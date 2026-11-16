@php
    $hasOldValues = $formId === 'new-address'
        ? ! old('address_id')
        : (int) old('address_id') === $address?->id;
@endphp

<input type="hidden" name="address_id" value="{{ $formId === 'new-address' ? '' : $address->id }}">
<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="{{ $formId }}-receiver-name">Người nhận</label>
        <input
            class="form-control {{ $hasOldValues ? ($errors->has('receiver_name') ? 'is-invalid' : '') : '' }}"
            id="{{ $formId }}-receiver-name"
            name="receiver_name"
            value="{{ $hasOldValues ? old('receiver_name', $address?->receiver_name) : $address?->receiver_name }}"
            autocomplete="name"
            maxlength="100"
            required
        >
        @if ($hasOldValues)
            @error('receiver_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="{{ $formId }}-phone">Số điện thoại</label>
        <input
            class="form-control {{ $hasOldValues ? ($errors->has('phone') ? 'is-invalid' : '') : '' }}"
            id="{{ $formId }}-phone"
            name="phone"
            type="tel"
            value="{{ $hasOldValues ? old('phone', $address?->phone) : $address?->phone }}"
            autocomplete="tel"
            maxlength="20"
            required
        >
        @if ($hasOldValues)
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="{{ $formId }}-province">Tỉnh / thành phố</label>
        <input
            class="form-control {{ $hasOldValues ? ($errors->has('province') ? 'is-invalid' : '') : '' }}"
            id="{{ $formId }}-province"
            name="province"
            value="{{ $hasOldValues ? old('province', $address?->province) : $address?->province }}"
            autocomplete="address-level1"
            maxlength="100"
            required
        >
        @if ($hasOldValues)
            @error('province')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="{{ $formId }}-district">Quận / huyện</label>
        <input
            class="form-control {{ $hasOldValues ? ($errors->has('district') ? 'is-invalid' : '') : '' }}"
            id="{{ $formId }}-district"
            name="district"
            value="{{ $hasOldValues ? old('district', $address?->district) : $address?->district }}"
            autocomplete="address-level2"
            maxlength="100"
            required
        >
        @if ($hasOldValues)
            @error('district')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="{{ $formId }}-ward">Phường / xã</label>
        <input
            class="form-control {{ $hasOldValues ? ($errors->has('ward') ? 'is-invalid' : '') : '' }}"
            id="{{ $formId }}-ward"
            name="ward"
            value="{{ $hasOldValues ? old('ward', $address?->ward) : $address?->ward }}"
            autocomplete="address-level3"
            maxlength="100"
            required
        >
        @if ($hasOldValues)
            @error('ward')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold" for="{{ $formId }}-detail-address">Địa chỉ cụ thể</label>
        <input
            class="form-control {{ $hasOldValues ? ($errors->has('detail_address') ? 'is-invalid' : '') : '' }}"
            id="{{ $formId }}-detail-address"
            name="detail_address"
            value="{{ $hasOldValues ? old('detail_address', $address?->detail_address) : $address?->detail_address }}"
            autocomplete="street-address"
            maxlength="255"
            required
        >
        @if ($hasOldValues)
            @error('detail_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>
    @if ($showDefault)
        <div class="col-12">
            <div class="form-check">
                <input
                    class="form-check-input"
                    id="{{ $formId }}-is-default"
                    name="is_default"
                    type="checkbox"
                    value="1"
                    @checked($hasOldValues ? old('is_default') : false)
                >
                <label class="form-check-label" for="{{ $formId }}-is-default">
                    Đặt làm địa chỉ mặc định
                </label>
            </div>
        </div>
    @endif
</div>
