@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá | Stationery Store')
@section('page-heading', 'Mã giảm giá')

@section('content')
    <div class="admin-page-heading">
        <div><span class="admin-eyebrow">CHƯƠNG TRÌNH ƯU ĐÃI</span><h1>Mã giảm giá</h1><p>Lượt sử dụng đã ghi nhận không thể chỉnh sửa thủ công.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.coupons.create') }}"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tạo mã</a>
    </div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách mã giảm giá</h2><p>{{ $coupons->total() }} mã</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="coupon-search">Tìm mã giảm giá</label>
                <input class="form-control form-control-sm" id="coupon-search" name="q" value="{{ request('q') }}" placeholder="Mã giảm giá">
                <select class="form-select form-select-sm" name="status" aria-label="Lọc trạng thái">
                    <option value="">Mọi trạng thái</option>
                    <option value="1" @selected(request('status') === '1')>Đang hoạt động</option>
                    <option value="0" @selected(request('status') === '0')>Đã tắt</option>
                </select>
                <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" title="Lọc dữ liệu" aria-label="Lọc dữ liệu"><i class="bi bi-funnel"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Mã</th><th>Ưu đãi</th><th>Thời hạn</th><th>Lượt dùng</th><th>Trạng thái</th><th class="text-center">Thao tác</th></tr></thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong><small class="d-block text-secondary">Tối thiểu {{ number_format((float) $coupon->min_order_value) }} ₫</small></td>
                            <td>{{ $coupon->discount_type === 'percentage' ? $coupon->discount_value.'%' : number_format((float) $coupon->discount_value).' ₫' }}</td>
                            <td>{{ $coupon->start_date->format('d/m/Y') }} – {{ $coupon->end_date->format('d/m/Y') }}</td>
                            <td>{{ $coupon->used_count }}{{ $coupon->usage_limit !== null ? ' / '.$coupon->usage_limit : '' }}</td>
                            <td><span class="badge {{ $coupon->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $coupon->status ? 'Đang bật' : 'Đã tắt' }}</span></td>
                            <td class="text-center"><div class="admin-row-actions justify-content-center">
                                <a class="btn btn-outline-primary btn-sm admin-icon-button" href="{{ route('admin.coupons.edit', $coupon) }}" title="Sửa mã giảm giá" aria-label="Sửa mã giảm giá"><i class="bi bi-pencil-square"></i></a>
                                <button
                                    class="btn btn-outline-success btn-sm admin-icon-button"
                                    type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#coupon-send-modal"
                                    data-coupon-id="{{ $coupon->id }}"
                                    data-coupon-code="{{ $coupon->code }}"
                                    data-coupon-send-url="{{ route('admin.coupons.send', $coupon) }}"
                                    title="Gửi mã cho khách hàng"
                                    aria-label="Gửi mã cho khách hàng"
                                >
                                    <i class="bi bi-send-fill"></i>
                                </button>
                                @if ($coupon->orders_count === 0 && $coupon->used_count === 0)
                                    <form id="delete-coupon-{{ $coupon->id }}" action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm admin-icon-button" type="button" data-bs-toggle="modal" data-bs-target="#admin-confirm-modal" data-admin-form="delete-coupon-{{ $coupon->id }}" data-admin-message="Xóa mã {{ $coupon->code }}?" title="Xóa mã giảm giá" aria-label="Xóa mã giảm giá"><i class="bi bi-trash3"></i></button>
                                    </form>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="admin-empty-note">Chưa có mã giảm giá phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $coupons->links() }}</div>
    </div>
@endsection
