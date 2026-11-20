@extends('layouts.admin')

@section('title', 'Quản lý đánh giá | Stationery Store')
@section('page-heading', 'Đánh giá')

@section('content')
    <div class="admin-page-heading"><div><span class="admin-eyebrow">PHẢN HỒI KHÁCH HÀNG</span><h1>Đánh giá</h1><p>Ẩn hoặc duyệt đánh giá; nội dung lịch sử được giữ lại.</p></div></div>
    <div class="admin-panel">
        <div class="admin-panel-heading">
            <div><h2>Danh sách đánh giá</h2><p>{{ $reviews->total() }} đánh giá</p></div>
            <form method="GET" class="admin-search-form">
                <label class="visually-hidden" for="review-status">Lọc trạng thái</label>
                <select class="form-select form-select-sm" id="review-status" name="status">
                    <option value="">Mọi trạng thái</option>
                    <option value="0" @selected(request('status') === '0')>Chờ duyệt</option>
                    <option value="1" @selected(request('status') === '1')>Đã duyệt</option>
                    <option value="2" @selected(request('status') === '2')>Đã ẩn</option>
                </select>
                <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" title="Lọc dữ liệu" aria-label="Lọc dữ liệu"><i class="bi bi-funnel"></i></button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Sản phẩm / Đơn hàng</th>
                        <th>Khách hàng</th>
                        <th class="text-center">Điểm</th>
                        <th>Nội dung</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>
                                <strong>{{ $review->product->name }}</strong>
                                <small class="d-block text-secondary">
                                    {{ $review->orderItem?->order?->order_code ? 'Đơn #'.$review->orderItem->order->order_code : 'Đánh giá đã mua hàng' }}
                                </small>
                            </td>
                            <td>{{ $review->user->full_name }}</td>
                            <td class="text-center">
                                <span class="fw-semibold text-warning">{{ $review->rating }}</span>
                                <span class="text-secondary">/5</span>
                            </td>
                            <td class="admin-review-comment">{{ $review->comment ?: '—' }}</td>
                            <td class="text-center">
                                @switch($review->status)
                                    @case(0)<span class="badge text-bg-warning">Chờ duyệt</span>@break
                                    @case(1)<span class="badge text-bg-success">Đã duyệt</span>@break
                                    @default<span class="badge text-bg-secondary">Đã ẩn</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="admin-review-status-form">
                                    @csrf @method('PUT')
                                    <textarea class="form-control form-control-sm mb-2" name="admin_reply" rows="2" maxlength="1000" placeholder="Phản hồi khách hàng">{{ $review->admin_reply }}</textarea>
                                    <button class="btn btn-outline-primary btn-sm admin-icon-button" type="submit" name="status" value="{{ $review->status }}" title="Lưu phản hồi" aria-label="Lưu phản hồi">
                                        <i class="bi bi-send"></i>
                                    </button>
                                    @if ($review->status !== 1)
                                        <button class="btn btn-outline-success btn-sm admin-icon-button" type="submit" name="status" value="1" title="Duyệt đánh giá" aria-label="Duyệt đánh giá">
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                    @endif
                                    @if ($review->status !== 2)
                                        <button class="btn btn-outline-secondary btn-sm admin-icon-button" type="submit" name="status" value="2" title="Ẩn đánh giá" aria-label="Ẩn đánh giá">
                                            <i class="bi bi-eye-slash"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="admin-empty-note">Chưa có đánh giá phù hợp.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $reviews->links() }}</div>
    </div>
@endsection
