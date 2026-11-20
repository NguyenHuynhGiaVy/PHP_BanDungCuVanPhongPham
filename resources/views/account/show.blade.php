@extends('layouts.store')

@section('title', 'Tài khoản của tôi | Việt Thịnh')

@section('content')
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tài khoản của tôi</li>
            </ol>
        </nav>

        <div class="mb-4">
            <h1 class="h4 fw-bold text-dark mb-1">Quản lý tài khoản</h1>
            <p class="text-muted small mb-0">Cập nhật thông tin cá nhân, mật khẩu và xem lịch sử đơn hàng của bạn</p>
        </div>

        <div class="row g-4 align-items-start">
            <!-- CỘT TRÁI: SIDEBAR TÀI KHOẢN -->
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>

            <!-- CỘT PHẢI: NỘI DUNG TÀI KHOẢN -->
            <div class="col-12 col-lg-9">
                <section class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-bell fs-5 text-primary"></i>
                        <h2 class="h5 fw-bold text-dark mb-0">Thông báo khuyến mãi</h2>
                    </div>
                    @if ($notifications->isNotEmpty())
                        <div class="d-flex flex-column gap-2">
                            @foreach ($notifications as $notification)
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3 {{ $notification->read_at ? 'bg-light' : 'bg-primary bg-opacity-10 border border-primary-subtle' }}">
                                    <i class="bi bi-ticket-perforated-fill text-primary fs-5"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block text-dark">{{ $notification->data['message'] ?? 'Bạn có thông báo mới.' }}</strong>
                                        @if (! empty($notification->data['code']))
                                            <code class="text-primary">{{ $notification->data['code'] }}</code>
                                        @endif
                                        <small class="d-block text-secondary mt-1">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if (! empty($notification->data['url']))
                                        <a class="btn btn-sm btn-outline-primary" href="{{ $notification->data['url'] }}">Dùng ngay</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @php
                            $user->unreadNotifications()->update(['read_at' => now()]);
                        @endphp
                    @else
                        <div class="text-center py-4 text-secondary">
                            <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                            <strong class="d-block text-dark">Chưa có thông báo</strong>
                            <small>Các mã khuyến mãi được gửi riêng sẽ hiển thị tại đây.</small>
                        </div>
                    @endif
                </section>
                <!-- 1. THÔNG TIN CÁ NHÂN -->
                <section class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-person-lines-fill fs-5 text-primary"></i>
                        <h2 class="h5 fw-bold text-dark mb-0">Thông tin cá nhân</h2>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('account.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-secondary" for="account-name">Họ và tên</label>
                                <input
                                    class="form-control @error('full_name') is-invalid @enderror"
                                    id="account-name"
                                    name="full_name"
                                    value="{{ old('full_name', $user->full_name) }}"
                                    autocomplete="name"
                                    maxlength="100"
                                    required
                                >
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-secondary" for="account-email">Email đăng nhập</label>
                                <input
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="account-email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="email"
                                    maxlength="150"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold text-secondary" for="account-phone">Số điện thoại</label>
                                <input
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="account-phone"
                                    name="phone"
                                    type="tel"
                                    value="{{ old('phone', $user->phone) }}"
                                    autocomplete="tel"
                                    maxlength="20"
                                    placeholder="Chưa cập nhật số điện thoại"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 pt-2">
                                <button class="btn btn-primary px-4 fw-semibold" type="submit">Lưu thay đổi</button>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- 2. ĐỔI MẬT KHẨU -->
                <section class="card border-0 shadow-sm rounded-3 p-4 mb-4" id="account-password">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="bi bi-shield-lock fs-5 text-primary"></i>
                        <h2 class="h5 fw-bold text-dark mb-0">Đổi mật khẩu bảo mật</h2>
                    </div>

                    <form action="{{ route('account.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-secondary" for="account-current-password">Mật khẩu hiện tại</label>
                                <input
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    id="account-current-password"
                                    name="current_password"
                                    type="password"
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    required
                                >
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-secondary" for="account-new-password">Mật khẩu mới</label>
                                <input
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="account-new-password"
                                    name="password"
                                    type="password"
                                    autocomplete="new-password"
                                    minlength="8"
                                    placeholder="Tối thiểu 8 ký tự"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold text-secondary" for="account-password-confirmation">Xác nhận mật khẩu</label>
                                <input
                                    class="form-control"
                                    id="account-password-confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    minlength="8"
                                    placeholder="Nhập lại mật khẩu mới"
                                    required
                                >
                            </div>

                            <div class="col-12 pt-2">
                                <button class="btn btn-outline-primary px-4 fw-semibold" type="submit">Cập nhật mật khẩu</button>
                            </div>
                        </div>
                    </form>
                </section>

                <!-- 3. ĐƠN HÀNG GẦN ĐÂY -->
                <section class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam fs-5 text-primary"></i>
                            <h2 class="h5 fw-bold text-dark mb-0">Đơn hàng gần đây</h2>
                        </div>
                        <a href="{{ route('orders.index') }}" class="small text-primary text-decoration-none fw-semibold">
                            Xem tất cả <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>

                    @forelse ($recentOrders as $order)
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-2 bg-light border mb-2 transition hover-shadow">
                            <div>
                                <a href="{{ route('orders.show', $order) }}" class="fw-bold text-dark text-decoration-none">
                                    #{{ $order->order_code }}
                                </a>
                                <small class="text-muted d-block" style="font-size: 0.78rem;">
                                    Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <strong class="text-danger d-block">{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</strong>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.7rem;">
                                    {{ $order->order_status }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-1 text-secondary"></i>
                            <p class="mb-0 small">Bạn chưa có đơn hàng nào.</p>
                        </div>
                    @endforelse
                </section>
            </div>
        </div>
    </div>
@endsection
