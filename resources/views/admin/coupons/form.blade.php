@extends('layouts.admin')

@php($isEditing = $coupon->exists)

@section('title', ($isEditing ? 'Sửa mã giảm giá' : 'Tạo mã giảm giá').' | Stationery Store')
@section('page-heading', 'Mã giảm giá')

@section('content')
    <div class="admin-page-heading"><div><span class="admin-eyebrow">CHƯƠNG TRÌNH ƯU ĐÃI</span><h1>{{ $isEditing ? 'Sửa mã giảm giá' : 'Tạo mã giảm giá' }}</h1><p>Lượt sử dụng không bị thay đổi khi lưu cấu hình.</p></div><a class="btn btn-outline-secondary admin-icon-button" href="{{ route('admin.coupons.index') }}" title="Quay lại danh sách" aria-label="Quay lại danh sách"><i class="bi bi-arrow-left"></i></a></div>
    <section class="admin-panel admin-form-panel">
        <form action="{{ $isEditing ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" method="POST">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="coupon-code">Mã giảm giá</label>
                    <input class="form-control @error('code') is-invalid @enderror" id="coupon-code" name="code" value="{{ old('code', $coupon->code) }}" maxlength="50" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="coupon-type">Loại giảm giá</label>
                    <select class="form-select" id="coupon-type" name="discount_type" required>
                        <option value="percentage" @selected(old('discount_type', $coupon->discount_type) === 'percentage')>Phần trăm</option>
                        <option value="fixed" @selected(old('discount_type', $coupon->discount_type) === 'fixed')>Số tiền cố định</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label" for="coupon-value">Giá trị</label>
                    <input class="form-control @error('discount_value') is-invalid @enderror" id="coupon-value" name="discount_value" type="number" min="0.01" step="0.01" value="{{ old('discount_value', $coupon->discount_value) }}" required>
                    @error('discount_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="coupon-min-order">Giá trị đơn tối thiểu (₫)</label>
                    <input class="form-control" id="coupon-min-order" name="min_order_value" type="number" min="0" step="0.01" value="{{ old('min_order_value', $coupon->min_order_value ?? 0) }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="coupon-max-discount">Mức giảm tối đa (₫)</label>
                    <input class="form-control @error('max_discount') is-invalid @enderror" id="coupon-max-discount" name="max_discount" type="number" min="0.01" step="0.01" value="{{ old('max_discount', $coupon->max_discount) }}">
                    @error('max_discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="coupon-limit">Giới hạn sử dụng</label>
                    <input class="form-control @error('usage_limit') is-invalid @enderror" id="coupon-limit" name="usage_limit" type="number" min="{{ $coupon->used_count }}" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                    @error('usage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Để trống nếu không giới hạn; hiện đã dùng {{ $coupon->used_count }} lần.</div>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="coupon-start">Bắt đầu</label>
                    <input class="form-control @error('start_date') is-invalid @enderror" id="coupon-start" name="start_date" type="datetime-local" value="{{ old('start_date', $coupon->start_date?->format('Y-m-d\TH:i')) }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="coupon-end">Kết thúc</label>
                    <input class="form-control @error('end_date') is-invalid @enderror" id="coupon-end" name="end_date" type="datetime-local" value="{{ old('end_date', $coupon->end_date?->format('Y-m-d\TH:i')) }}" required>
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label" for="coupon-status">Trạng thái</label>
                    <select class="form-select" id="coupon-status" name="status" required>
                        <option value="1" @selected((string) old('status', $coupon->status) === '1')>Đang bật</option>
                        <option value="0" @selected((string) old('status', $coupon->status) === '0')>Đã tắt</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end pt-3 border-top"><button class="btn btn-primary admin-save-button" type="submit"><i class="bi bi-check2-circle me-1"></i>{{ $isEditing ? 'Lưu thay đổi' : 'Tạo mã' }}</button></div>
            </div>
        </form>
    </section>
@endsection
