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
                <button class="btn btn-outline-primary btn-sm" type="submit">Lọc</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table admin-table align-middle mb-0">
                <thead><tr><th>Sản phẩm</th><th>Khách hàng</th><th>Điểm</th><th>Nội dung</th><th>Trạng thái</th><th>Cập nhật</th></tr></thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->product->name }}</td>
                            <td>{{ $review->user->full_name }}</td>
                            <td>{{ $review->rating }} / 5</td>
                            <td class="admin-review-comment">{{ $review->comment ?: '—' }}</td>
                            <td>
                                @switch($review->status)
                                    @case(0)<span class="badge text-bg-warning">Chờ duyệt</span>@break
                                    @case(1)<span class="badge text-bg-success">Đã duyệt</span>@break
                                    @default<span class="badge text-bg-secondary">Đã ẩn</span>
                                @endswitch
                            </td>
                            <td>
                                <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="admin-review-status-form">
                                    @csrf @method('PUT')
                                    <select class="form-select form-select-sm" name="status" aria-label="Trạng thái đánh giá">
                                        <option value="0" @selected($review->status === 0)>Chờ duyệt</option>
                                        <option value="1" @selected($review->status === 1)>Duyệt</option>
                                        <option value="2" @selected($review->status === 2)>Ẩn</option>
                                    </select>
                                    <button class="btn btn-outline-primary btn-sm" type="submit">Lưu</button>
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
