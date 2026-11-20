<footer class="store-footer mt-5" style="background-color: #f8fafc;">
    <!-- Newsletter Signup -->
    <div class="py-4" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="fw-bold fs-5 text-nowrap" style="color: #1e293b;">Đăng ký nhận tin</div>

            <div class="flex-grow-1 mx-md-4 w-100" style="max-width: 500px;">
                <form onsubmit="event.preventDefault(); this.querySelector('.form-control').value=''; alert('Cảm ơn bạn đã đăng ký!');" class="d-flex bg-white rounded-pill p-1" style="border: 1px solid #cbd5e1;">
                    <input type="email" class="form-control border-0 rounded-pill shadow-none ps-4" placeholder="Nhập email của bạn" required style="height: 38px;">
                    <button class="btn btn-primary rounded-pill px-4 fw-bold text-nowrap" type="submit" style="font-size: 0.85rem;">ĐĂNG KÝ</button>
                </form>
            </div>

            <div class="d-flex align-items-center gap-3 text-nowrap">
                <span class="fw-bold me-1" style="color: #1e293b;">Kết nối:</span>
                <a href="#" style="color: #1e40af;"><i class="bi bi-facebook fs-5"></i></a>
                <a href="#" style="color: #1e40af;"><i class="bi bi-youtube fs-5"></i></a>
                <a href="#" style="color: #1e40af;"><i class="bi bi-instagram fs-5"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Footer Info -->
    <div class="container py-5" style="background-color: #f8fafc;">
        <div class="row g-4">
            <div class="col-12 col-lg-5">
                <h6 class="text-dark fw-bold mb-3 text-nowrap">Việt Thịnh - Giải pháp văn phòng toàn diện</h6>
                <p class="small mb-2 text-muted">Công Ty TNHH TM-DV Việt Thịnh</p>
                <p class="small mb-2 text-muted">GPĐKKD số 0305341389 do Sở Kế Hoạch và Đầu Tư TP. HCM cấp.</p>
                <div class="d-flex flex-column gap-2 mt-3 small text-muted">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
                        <span>123 Đường Bùi Thị Xuân, Phường Bến Thành, Quận 1, TP. HCM</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-primary"></i>
                        <span>1900 1288</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-fill text-primary"></i>
                        <span>hotro@vietthinh.vn</span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="text-dark fw-bold mb-3 text-nowrap">THÔNG TIN HỖ TRỢ</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small m-0 p-0">
                    <li><a href="#" class="text-muted text-decoration-none hover-primary">Chính sách thanh toán</a></li>
                    <li><a href="#" class="text-muted text-decoration-none hover-primary">Chính sách giao hàng</a></li>
                    <li><a href="#" class="text-muted text-decoration-none hover-primary">Chính sách đổi trả</a></li>
                    <li><a href="#" class="text-muted text-decoration-none hover-primary">Bảo mật thông tin</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="text-dark fw-bold mb-3 text-nowrap">Về Việt Thịnh</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small m-0 p-0">
                    <li><a href="{{ route('about') }}" class="text-muted text-decoration-none hover-primary">Giới thiệu</a></li>
                    <li><a href="{{ route('contact') }}" class="text-muted text-decoration-none hover-primary">Thông tin liên hệ</a></li>
                </ul>

                <div class="mt-4">
                    <h6 class="text-dark fw-bold mb-2 small text-nowrap">Phương thức thanh toán:</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white border text-dark py-2 px-3"><i class="bi bi-cash-coin text-success fs-5"></i></span>
                        <span class="badge bg-white border text-dark py-2 px-3"><i class="bi bi-credit-card text-primary fs-5"></i></span>
                        <span class="badge bg-white border text-dark py-2 px-3"><i class="bi bi-bank text-danger fs-5"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="py-3 text-center small" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; color: #64748b;">
        Copyright © {{ now()->year }} Việt Thịnh - Bản quyền thuộc Công Ty TNHH TM-DV Việt Thịnh
    </div>
</footer>