@extends('layouts.admin')

@section('title', 'Quản lý nhân viên & Thành viên | Admin')
@section('page-heading', 'Nhân viên')

@section('content')
    <div class="admin-page-heading">
        <div>
            <span class="admin-eyebrow">HỆ THỐNG & PHÂN QUYỀN</span>
            <h1>Quản lý nhân viên</h1>
            <p>Quản lý tài khoản quản trị viên và nhân viên vận hành cửa hàng.</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i>Thêm nhân viên
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
            <span>{{ session('status') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm mb-4" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div>
                <h2>Danh sách nhân viên</h2>
                <p>{{ $staffUsers->total() }} nhân sự</p>
            </div>
            <form action="{{ route('admin.staff.index') }}" method="GET" class="admin-search-form">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    class="form-control form-control-sm"
                    placeholder="Tên, email hoặc điện thoại"
                    data-live-table-filter="#staff-table"
                >
                @if ($search)
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm">Xóa</a>
                @endif
                <button class="btn btn-outline-primary btn-sm" type="submit">Tìm</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0" id="staff-table">
                <thead>
                    <tr>
                        <th class="ps-3 py-3">Họ và tên</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Số điện thoại</th>
                        <th class="py-3">Vai trò</th>
                        <th class="py-3">Trạng thái</th>
                        <th class="py-3">Ngày tạo</th>
                        <th class="py-3 text-center pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staffUsers as $user)
                        <tr>
                            <td class="ps-3 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                        <i class="bi bi-person-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block text-dark">{{ $user->full_name }}</strong>
                                        @if ($user->id === auth()->id())
                                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">Bạn</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'Chưa cập nhật' }}</td>
                            <td>
                                @if ($user->role?->name === 'ADMIN')
                                    <span class="badge bg-danger text-white px-2 py-1">Quản trị viên</span>
                                @else
                                    <span class="badge bg-primary text-white px-2 py-1">Nhân viên</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->status === 1)
                                    <span class="badge bg-success-subtle text-success px-2 py-1">Đang hoạt động</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">Đã khóa</span>
                                @endif
                            </td>
                            <td class="small text-secondary">{{ $user->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.staff.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Chỉnh sửa">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.staff.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">
                                Không tìm thấy nhân viên nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($staffUsers->hasPages())
            <div class="mt-4">
                {{ $staffUsers->links() }}
            </div>
        @endif
    </div>
@endsection
