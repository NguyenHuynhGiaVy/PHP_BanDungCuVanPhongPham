@extends('layouts.admin')

@section('title', ($isEdit ? 'Chỉnh sửa nhân viên' : 'Thêm nhân viên mới') . ' | Admin')
@section('page-heading', 'Nhân viên')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">HỆ THỐNG & PHÂN QUYỀN</span>
            <h1>{{ $isEdit ? 'Chỉnh sửa nhân viên' : 'Thêm nhân viên' }}</h1>
            <p>Cung cấp tài khoản truy cập vào khu vực quản trị cửa hàng.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary admin-icon-button" title="Quay lại danh sách" aria-label="Quay lại danh sách">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-panel admin-form-panel">
        <form action="{{ $isEdit ? route('admin.staff.update', $staff) : route('admin.staff.store') }}" method="POST">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="staff-role" class="form-label fw-semibold">Vai trò / Quyền hạn <span class="text-danger">*</span></label>
                    <select class="form-select @error('role_id') is-invalid @enderror" id="staff-role" name="role_id" required>
                        <option value="">-- Chọn vai trò --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id', $staff->role_id) == $role->id)>
                                {{ $role->name === 'ADMIN' ? 'Quản trị viên (Toàn quyền)' : 'Nhân viên (Theo phân quyền)' }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="staff-status" class="form-label fw-semibold">Trạng thái tài khoản <span class="text-danger">*</span></label>
                    <select class="form-select" id="staff-status" name="status" required>
                        <option value="1" @selected(old('status', $staff->status ?? 1) == 1)>Đang hoạt động</option>
                        <option value="0" @selected(old('status', $staff->status ?? 1) == 0)>Khóa tài khoản</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="staff-name" class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        class="form-control @error('full_name') is-invalid @enderror"
                        id="staff-name"
                        name="full_name"
                        value="{{ old('full_name', $staff->full_name) }}"
                        placeholder="Ví dụ: Nguyễn Văn Bảo"
                        required
                    >
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="staff-email" class="form-label fw-semibold">Địa chỉ Email <span class="text-danger">*</span></label>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="staff-email"
                        name="email"
                        value="{{ old('email', $staff->email) }}"
                        placeholder="nhanvien@stationery.com"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="staff-phone" class="form-label fw-semibold">Số điện thoại</label>
                    <input
                        type="tel"
                        class="form-control @error('phone') is-invalid @enderror"
                        id="staff-phone"
                        name="phone"
                        value="{{ old('phone', $staff->phone) }}"
                        placeholder="0901234567"
                    >
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="staff-password" class="form-label fw-semibold">
                        Mật khẩu {{ $isEdit ? '(Để trống nếu không muốn đổi)' : '*' }}
                    </label>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="staff-password"
                        name="password"
                        placeholder="Tối thiểu 8 ký tự"
                        {{ $isEdit ? '' : 'required' }}
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary admin-save-button">
                    <i class="bi bi-check2-circle me-1"></i>{{ $isEdit ? 'Cập nhật' : 'Thêm nhân viên' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
