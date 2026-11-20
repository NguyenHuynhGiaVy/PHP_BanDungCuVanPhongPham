@extends('layouts.store')

@section('title', 'Giới thiệu Việt Thịnh | Văn phòng phẩm & dụng cụ văn phòng chính hãng')



@section('content')
@php
// TODO: thay bằng mốc phát triển thật của Việt Thịnh
$milestones = [
['year' => '2018', 'title' => 'Thành lập', 'text' => 'Khởi đầu từ một cửa hàng nhỏ tại TP. Hồ Chí Minh, chuyên bút viết và vở học sinh.'],
['year' => '2020', 'title' => 'Mở rộng danh mục', 'text' => 'Bổ sung giấy in, file hồ sơ, dụng cụ văn phòng và hợp tác thêm nhiều thương hiệu chính hãng.'],
['year' => '2022', 'title' => 'Phục vụ doanh nghiệp', 'text' => 'Triển khai báo giá theo số lượng và giao hàng định kỳ cho công ty, trường học.'],
['year' => '2024', 'title' => 'Bán hàng trực tuyến', 'text' => 'Ra mắt website, giao hàng toàn quốc và xuất hóa đơn VAT điện tử.'],
['year' => 'Hôm nay', 'title' => 'Tiếp tục phát triển', 'text' => 'Hoàn thiện dịch vụ, mở rộng danh mục và nâng cao trải nghiệm mua sắm cho khách hàng.'],
];

// TODO: đặt ảnh thật vào public/images/about/ (nếu thiếu ảnh, khung xám sẽ hiển thị thay thế)
$gallery = [
['src' => 'images/about/cua-hang.jpg', 'caption' => 'Cửa hàng Việt Thịnh'],
['src' => 'images/about/kho-hang.jpg', 'caption' => 'Kho hàng'],
['src' => 'images/about/dong-goi.jpg', 'caption' => 'Đóng gói đơn hàng'],
['src' => 'images/about/doi-ngu.jpg', 'caption' => 'Đội ngũ tư vấn'],
['src' => 'images/about/san-pham.jpg', 'caption' => 'Sản phẩm chính hãng'],
['src' => 'images/about/giao-hang.jpg', 'caption' => 'Giao hàng toàn quốc'],
];
@endphp
<div class="vt-page">

    {{-- Breadcrumb --}}
    <div class="container pt-3">
        <nav class="store-breadcrumb" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
            </ol>
        </nav>
    </div>

    {{-- 1. Giới thiệu công ty --}}
    <section class="vt-section">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-7">
                    <span class="vt-eyebrow">Về Việt Thịnh</span>
                    <h1 class="vt-title">Đồng hành cùng học tập và công việc mỗi ngày</h1>
                    <p class="vt-lead">
                        Việt Thịnh chuyên phân phối bút viết, sổ vở, giấy in, file hồ sơ, dụng cụ học tập
                        và thiết bị văn phòng chính hãng từ Thiên Long, Deli, Pentel, Casio, Double A…
                    </p>
                    <p class="text-secondary">
                        Chúng tôi phục vụ học sinh, sinh viên, giáo viên, cơ quan và doanh nghiệp trên toàn quốc
                        với mục tiêu rõ ràng: sản phẩm chính hãng, giá minh bạch, giao hàng đúng hẹn và
                        hỗ trợ chứng từ đầy đủ.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('products.index') }}" class="btn btn-primary px-4">Xem sản phẩm</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-dark px-4">Liên hệ báo giá</a>
                    </div>
                </div>

                <div class="col-lg-5">
                    {{-- TODO: bổ sung tên pháp lý, mã số thuế, năm thành lập nếu muốn hiển thị --}}
                    <dl class="vt-facts">
                        <div>
                            <dt>Lĩnh vực</dt>
                            <dd>Văn phòng phẩm &amp; thiết bị văn phòng</dd>
                        </div>
                        <div>
                            <dt>Khách hàng</dt>
                            <dd>Cá nhân, trường học, cơ quan, doanh nghiệp</dd>
                        </div>
                        <div>
                            <dt>Địa chỉ</dt>
                            <dd>123 Đường Bùi Thị Xuân, P. Bến Thành, Q.1, TP. Hồ Chí Minh</dd>
                        </div>
                        <div>
                            <dt>Hotline</dt>
                            <dd><a href="tel:19001288">1900 1288</a></dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd><a href="mailto:hotro@vietthinh.vn">hotro@vietthinh.vn</a></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Giá trị cốt lõi --}}
    <section class="vt-section vt-section-tint">
        <div class="container">
            <div class="vt-heading">
                <span class="vt-eyebrow">Giá trị cốt lõi</span>
                <h2 class="vt-h2">Điều chúng tôi cam kết</h2>
            </div>
            <div class="row g-3 g-lg-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="vt-value">
                        <i class="bi bi-patch-check" aria-hidden="true"></i>
                        <h3>Hàng chính hãng</h3>
                        <p>Có tem nhãn, nguồn gốc rõ ràng và đầy đủ hóa đơn chứng từ.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="vt-value">
                        <i class="bi bi-tag" aria-hidden="true"></i>
                        <h3>Giá minh bạch</h3>
                        <p>Niêm yết rõ ràng, chiết khấu theo số lượng cho trường học và doanh nghiệp.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="vt-value">
                        <i class="bi bi-truck" aria-hidden="true"></i>
                        <h3>Giao hàng toàn quốc</h3>
                        <p>Đóng gói chống sốc, giao tận nơi, theo dõi được tình trạng đơn hàng.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="vt-value">
                        <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
                        <h3>Đổi trả trong 7 ngày</h3>
                        <p>Áp dụng khi sản phẩm lỗi do nhà sản xuất hoặc hư hỏng khi vận chuyển.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Chặng đường phát triển --}}
    <section class="vt-section">
        <div class="container">
            <div class="vt-heading">
                <span class="vt-eyebrow">Chặng đường phát triển</span>
                <h2 class="vt-h2">Từ cửa hàng nhỏ đến nhà cung cấp tin cậy</h2>
            </div>

            <ol class="vt-timeline">
                @foreach ($milestones as $milestone)
                <li class="vt-timeline-item">
                    <span class="vt-timeline-year">{{ $milestone['year'] }}</span>
                    <div class="vt-timeline-body">
                        <h3>{{ $milestone['title'] }}</h3>
                        <p>{{ $milestone['text'] }}</p>
                    </div>
                </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 4. Hình ảnh công ty --}}
    <section class="vt-section vt-section-tint">
        <div class="container">
            <div class="vt-heading">
                <span class="vt-eyebrow">Hình ảnh</span>
                <h2 class="vt-h2">Việt Thịnh qua những khung hình</h2>
            </div>

            <div class="vt-gallery">
                @foreach ($gallery as $index => $item)
                <figure class="vt-gallery-item">
                    <button
                        type="button"
                        class="vt-gallery-open"
                        data-bs-toggle="modal"
                        data-bs-target="#vt-gallery-modal"
                        data-src="{{ asset($item['src']) }}"
                        data-caption="{{ $item['caption'] }}"
                        aria-label="Xem ảnh: {{ $item['caption'] }}">
                        <span class="vt-gallery-ph" aria-hidden="true"><i class="bi bi-image"></i></span>
                        <img
                            src="{{ asset($item['src']) }}"
                            alt="{{ $item['caption'] }}"
                            loading="lazy"
                            onerror="this.remove()">
                    </button>
                    <figcaption>{{ $item['caption'] }}</figcaption>
                </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. Kêu gọi hành động --}}
    <section class="vt-section">
        <div class="container">
            <div class="vt-cta">
                <div>
                    <h2>Cần báo giá cho công ty hoặc trường học?</h2>
                    <p>Gửi yêu cầu để nhận bảng giá theo số lượng và phương án giao hàng định kỳ.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('contact') }}" class="btn btn-primary px-4">Gửi yêu cầu báo giá</a>
                    {{-- TODO: thay bằng hotline thật --}}
                    <a href="tel:19001288" class="btn btn-outline-dark px-4">Gọi 1900 1288</a>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- Xem ảnh lớn --}}
<div class="modal fade" id="vt-gallery-modal" tabindex="-1" aria-label="Xem ảnh" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-body p-2">
                <img id="vt-gallery-modal-img" class="w-100 rounded" alt="" src="">
                <p id="vt-gallery-modal-caption" class="small text-secondary text-center mt-2 mb-1"></p>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('vt-gallery-modal')?.addEventListener('show.bs.modal', function(event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;
        this.querySelector('#vt-gallery-modal-img').src = trigger.dataset.src;
        this.querySelector('#vt-gallery-modal-caption').textContent = trigger.dataset.caption || '';
    });
</script>
@endsection