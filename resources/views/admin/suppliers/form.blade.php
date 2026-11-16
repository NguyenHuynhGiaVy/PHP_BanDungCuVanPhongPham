@extends('layouts.admin')

@php($isEditing = $supplier->exists)

@section('title', ($isEditing ? 'Sửa nhà cung cấp' : 'Thêm nhà cung cấp').' | Stationery Store')
@section('page-heading', 'Nhà cung cấp')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">DANH MỤC & SẢN PHẨM</span><h1>{{ $isEditing ? 'Sửa nhà cung cấp' : 'Thêm nhà cung cấp' }}</h1><p>Thông tin liên hệ và trạng thái đối tác.</p></div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.suppliers.index') }}">Quay lại</a>
    </div>
    <section class="admin-panel admin-form-panel">
        <form action="{{ $isEditing ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}" method="POST">
            @csrf
            @if ($isEditing) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="supplier-name">Tên nhà cung cấp</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="supplier-name" name="name" value="{{ old('name', $supplier->name) }}" maxlength="150" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="supplier-email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="supplier-email" name="email" type="email" value="{{ old('email', $supplier->email) }}" maxlength="150">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="supplier-phone">Số điện thoại</label>
                    <input class="form-control @error('phone') is-invalid @enderror" id="supplier-phone" name="phone" type="tel" value="{{ old('phone', $supplier->phone) }}" maxlength="20">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label" for="supplier-status">Trạng thái</label>
                    <select class="form-select" id="supplier-status" name="status" required>
                        <option value="1" @selected((string) old('status', $supplier->status) === '1')>Đang hoạt động</option>
                        <option value="0" @selected((string) old('status', $supplier->status) === '0')>Đã ẩn</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label" for="supplier-address">Địa chỉ</label>
                    <textarea class="form-control" id="supplier-address" name="address" rows="3" maxlength="255">{{ old('address', $supplier->address) }}</textarea>
                </div>
                <div class="col-12"><button class="btn btn-primary" type="submit">{{ $isEditing ? 'Lưu thay đổi' : 'Tạo nhà cung cấp' }}</button></div>
            </div>
        </form>
    </section>
@endsection
