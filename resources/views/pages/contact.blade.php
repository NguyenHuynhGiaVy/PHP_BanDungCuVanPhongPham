@extends('layouts.store')

@section('title', 'Liên hệ & Hỗ trợ khách hàng - Việt Thịnh')

@section('content')
<div class="vt-page">

    {{-- Breadcrumb --}}
    <div class="container pt-3">
        <nav class="store-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
            </ol>
        </nav>
    </div>

    <div class="container py-4 py-lg-5">

        {{-- Tiêu đề --}}
        <div class="vt-heading mb-4">
            <span class="vt-eyebrow">Hỗ trợ khách hàng</span>
            <h1 class="vt-title mb-2">Liên hệ với Việt Thịnh</h1>
            <p class="text-secondary mb-0 col-lg-7 px-0">
                Gửi câu hỏi về sản phẩm, đơn hàng hoặc yêu cầu báo giá. Chúng tôi phản hồi trong vòng 24 giờ làm việc.
            </p>
        </div>

        @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
        @endif

        {{-- Thông tin nhanh --}}
        {{-- TODO: thay địa chỉ / hotline / email / giờ làm việc bằng thông tin thật --}}
        <div class="row g-3 mb-4 mb-lg-5">
            <div class="col-sm-6 col-lg-3">
                <div class="vt-info">
                    <span class="vt-info-icon"><i class="bi bi-telephone" aria-hidden="true"></i></span>
                    <div>
                        <h2>Hotline</h2>
                        <p><a href="tel:19001288" class="fw-semibold">1900 1288</a></p>
                        <small>Bán lẻ, bán sỉ, tra cứu đơn hàng</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="vt-info">
                    <span class="vt-info-icon"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                    <div>
                        <h2>Email</h2>
                        <p><a href="mailto:hotro@vietthinh.vn">hotro@vietthinh.vn</a></p>
                        <small>Báo giá: <a href="mailto:baogia@vietthinh.vn">baogia@vietthinh.vn</a></small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="vt-info">
                    <span class="vt-info-icon"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                    <div>
                        <h2>Địa chỉ</h2>
                        <p>123 Đường Bùi Thị Xuân, P. Bến Thành, Q.1, TP. Hồ Chí Minh</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="vt-info">
                    <span class="vt-info-icon"><i class="bi bi-clock" aria-hidden="true"></i></span>
                    <div>
                        <h2>Giờ làm việc</h2>
                        <p>Thứ 2 – Thứ 7: 08:00 – 21:30</p>
                        <small>Chủ nhật: 08:00 – 17:00</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- Form liên hệ (giữ nguyên name field để không phải sửa controller) --}}
            <div class="col-lg-7">
                <div class="vt-card">
                    <h2 class="vt-card-title">Gửi tin nhắn cho chúng tôi</h2>
                    <p class="text-secondary small mb-4">
                        Các trường có dấu <span class="text-danger">*</span> là bắt buộc.
                    </p>

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="contact-name" class="form-label small fw-semibold">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    id="contact-name"
                                    name="name"
                                    value="{{ old('name', auth()->user()?->full_name) }}"
                                    placeholder="Ví dụ: Nguyễn Văn An"
                                    required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact-phone" class="form-label small fw-semibold">
                                    Số điện thoại <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="tel"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="contact-phone"
                                    name="phone"
                                    value="{{ old('phone', auth()->user()?->phone) }}"
                                    placeholder="0901234567"
                                    required>
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact-email" class="form-label small fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="contact-email"
                                    name="email"
                                    value="{{ old('email', auth()->user()?->email) }}"
                                    placeholder="email@example.com"
                                    required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="contact-subject" class="form-label small fw-semibold">
                                    Chủ đề <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('subject') is-invalid @enderror"
                                    id="contact-subject"
                                    name="subject"
                                    value="{{ old('subject') }}"
                                    list="contact-subject-suggestions"
                                    placeholder="Chọn gợi ý hoặc tự nhập chủ đề"
                                    required>
                                <datalist id="contact-subject-suggestions">
                                    <option value="Báo giá văn phòng phẩm số lượng lớn"></option>
                                    <option value="Báo giá dụng cụ học tập cho trường học"></option>
                                    <option value="Tra cứu tình trạng đơn hàng"></option>
                                    <option value="Đổi trả / bảo hành sản phẩm"></option>
                                    <option value="Yêu cầu xuất hóa đơn VAT"></option>
                                    <option value="Hợp tác phân phối / nhà cung cấp"></option>
                                </datalist>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="contact-message" class="form-label small fw-semibold">
                                    Nội dung <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    class="form-control @error('message') is-invalid @enderror"
                                    id="contact-message"
                                    name="message"
                                    rows="6"
                                    placeholder="Nêu rõ tên sản phẩm, số lượng, thời gian cần hàng (nếu có)."
                                    required>{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-4">
                            <p class="small text-secondary mb-0">
                                <i class="bi bi-lock me-1" aria-hidden="true"></i>Thông tin chỉ dùng để phản hồi yêu cầu này.
                            </p>
                            <button type="submit" class="btn btn-primary px-4 py-2">Gửi yêu cầu</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bản đồ + báo giá doanh nghiệp --}}
            <div class="col-lg-5">
                <div class="vt-card p-0 overflow-hidden mb-4">
                    {{-- TODO: thay src bằng link nhúng đúng địa chỉ Việt Thịnh --}}
                    <iframe
                        class="vt-map"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.561446948383!2d106.6866254!3d10.7682428!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f22a08fc81f%3A0x774d3bca5b480a4c!2zMTIzIELDuWkgVGjhu4sgWHXDom4sIELhur9uIFRow6BuaCwgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1784802702960!5m2!1svi!2s"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Vị trí cửa hàng Việt Thịnh"></iframe>
                </div>

                <div class="vt-card vt-card-soft">
                    <h2 class="vt-card-title mb-2">Khách hàng doanh nghiệp</h2>
                    <p class="text-secondary small mb-3">
                        Trường học, cơ quan và công ty có thể nhận báo giá theo số lượng,
                        hóa đơn VAT điện tử và giao hàng định kỳ.
                    </p>
                    <a href="mailto:baogia@vietthinh.vn" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-envelope me-1" aria-hidden="true"></i>Gửi email báo giá
                    </a>
                </div>
            </div>
        </div>

        {{-- FAQ --}}
        <section class="mt-5" id="faq">
            <div class="vt-heading">
                <span class="vt-eyebrow">Câu hỏi thường gặp</span>
                <h2 class="vt-h2">Có thể bạn đang tìm</h2>
            </div>

            <div class="row g-3">
                <div class="col-lg-6 d-grid gap-3 align-content-start">
                    <details class="store-faq-item vt-faq" open>
                        <summary>Việt Thịnh có giao hàng toàn quốc không?</summary>
                        <p>
                            Có. Chúng tôi hợp tác với các đơn vị vận chuyển để giao hàng trên 63 tỉnh thành,
                            thời gian từ 1–3 ngày làm việc tùy khu vực.
                        </p>
                    </details>
                    <details class="store-faq-item vt-faq">
                        <summary>Chính sách đổi trả như thế nào?</summary>
                        <p>
                            Đổi trả trong 7 ngày kể từ khi nhận hàng nếu sản phẩm hư hỏng do vận chuyển hoặc
                            lỗi từ nhà sản xuất. Vui lòng giữ lại hóa đơn và bao bì.
                        </p>
                    </details>
                    <details class="store-faq-item vt-faq">
                        <summary>Thiết bị văn phòng có được bảo hành không?</summary>
                        <p>
                            Các thiết bị như máy tính Casio, máy ép plastic, máy hủy giấy được bảo hành chính hãng
                            theo quy định của từng sản phẩm. Chúng tôi hỗ trợ tiếp nhận và chuyển máy đến trung tâm bảo hành.
                        </p>
                    </details>
                </div>
                <div class="col-lg-6 d-grid gap-3 align-content-start">
                    <details class="store-faq-item vt-faq">
                        <summary>Mua số lượng ít có được không? Đơn sỉ có chiết khấu không?</summary>
                        <p>
                            Chúng tôi bán lẻ từ 1 sản phẩm. Với đơn của công ty, trường học hoặc đơn định kỳ,
                            Việt Thịnh áp dụng chiết khấu theo số lượng. Vui lòng gửi yêu cầu để nhận báo giá.
                        </p>
                    </details>
                    <details class="store-faq-item vt-faq">
                        <summary>Việt Thịnh có xuất hóa đơn VAT điện tử không?</summary>
                        <p>
                            Có. Bạn chỉ cần cung cấp mã số thuế, tên công ty và địa chỉ khi đặt hàng,
                            hoặc gửi thông tin cho bộ phận hỗ trợ sau khi thanh toán.
                        </p>
                    </details>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection