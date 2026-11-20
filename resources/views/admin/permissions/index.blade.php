@extends('layouts.admin')

@section('title', 'Phân quyền sử dụng theo loại tài khoản | Admin')
@section('page-heading', 'Phân quyền')

@section('content')
<div class="container-fluid px-0">
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">HỆ THỐNG & PHÂN QUYỀN</span>
            <h1>Phân quyền sử dụng</h1>
            <p>Thiết lập quyền thao tác cho từng loại tài khoản.</p>
        </div>
    </div>
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
            <span>{{ session('status') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="admin-panel">
        <form action="{{ route('admin.permissions.update') }}" method="POST">
            @csrf

                    <!-- Bảng Ma Trận -->
                    <div class="table-responsive">
                        <table class="table admin-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 46%;" class="ps-3">Phân quyền</th>
                                    <th style="width: 18%;" class="text-center">
                                        Quản lý
                                        <small class="d-block text-secondary fw-normal">(Admin)</small>
                                    </th>
                                    <th style="width: 18%;" class="text-center">
                                        Thành viên
                                        <small class="d-block text-secondary fw-normal">(Nhân viên)</small>
                                    </th>
                                    <th style="width: 18%;" class="text-center">
                                        Khách
                                        <small class="d-block text-secondary fw-normal">(Khách hàng)</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    @php
                                        $staffHasPerm = $staffRole && $staffRole->permissions->contains('id', $permission->id);
                                        $customerHasPerm = $customerRole && $customerRole->permissions->contains('id', $permission->id);
                                    @endphp
                                    <tr class="border-bottom">
                                        <!-- Cột Phân quyền: Tên in đậm + Mô tả xám bên dưới -->
                                        <td class="ps-3 py-3">
                                            <div class="fw-bold text-dark mb-1 fs-6">
                                                {{ $permission->name }}
                                            </div>
                                            <div class="text-secondary small" style="font-size: 0.85rem;">
                                                {{ $permission->description }}
                                            </div>
                                        </td>

                                        <!-- Cột Quản lý (Admin): Luôn có quyền (Checkbox xám cố định như ảnh) -->
                                        <td class="text-center py-3">
                                            <span class="perm-checkbox-admin" title="Quản trị viên luôn có toàn quyền"></span>
                                        </td>

                                        <!-- Cột Thành viên (Staff): Checkbox xanh lá cây -->
                                        <td class="text-center py-3">
                                            @if ($staffRole)
                                                <input
                                                    type="checkbox"
                                                    class="perm-checkbox"
                                                    name="matrix[{{ $staffRole->id }}][{{ $permission->id }}]"
                                                    value="1"
                                                    @checked($staffHasPerm)
                                                    title="Bật/Tắt quyền cho Nhân viên"
                                                >
                                            @endif
                                        </td>

                                        <!-- Cột Khách (Customer): Checkbox xanh lá cây -->
                                        <td class="text-center py-3">
                                            @if ($customerRole)
                                                <input
                                                    type="checkbox"
                                                    class="perm-checkbox"
                                                    name="matrix[{{ $customerRole->id }}][{{ $permission->id }}]"
                                                    value="1"
                                                    @checked($customerHasPerm)
                                                    title="Bật/Tắt quyền cho Khách hàng"
                                                >
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

            <div class="d-flex justify-content-end px-3 py-3 border-top">
                <button type="submit" class="btn btn-primary admin-save-button">
                    <i class="bi bi-check2-circle me-1"></i>Lưu phân quyền
                </button>
            </div>
        </form>
        </div>
</div>
@endsection
