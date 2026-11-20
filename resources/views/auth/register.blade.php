@extends('layouts.store')

@section('title', 'Đăng ký tài khoản | Việt Thịnh')

@section('content')
<div class="auth-page-wrapper" style="background-image: url('{{ asset('images/auth-bg.gif') }}');">
    <div class="auth-page-overlay"></div>

    <div class="auth-card auth-card-wide">
        <!-- Logo & Title -->
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Việt Thịnh" style="height: 52px; width: auto; object-fit: contain;">
            </a>
            <h1 class="h4 fw-bold text-dark mb-1">Tạo tài khoản mới</h1>
            <p class="text-muted small mb-0">Đăng ký thành viên để tích điểm và nhận ưu đãi độc quyền</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1 fw-semibold">
                <i class="bi bi-exclamation-triangle-fill"></i> Có lỗi xảy ra:
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST" data-auth-form>
            @csrf

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="register-name">Họ và tên</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input
                        class="form-control @error('full_name') is-invalid @enderror"
                        id="register-name"
                        name="full_name"
                        type="text"
                        value="{{ old('full_name') }}"
                        autocomplete="name"
                        maxlength="100"
                        placeholder="Nguyễn Văn A"
                        required
                        autofocus>
                </div>
                @error('full_name')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email & Phone (2 cột trên desktop) -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary" for="register-email">Email</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input
                            class="form-control @error('email') is-invalid @enderror"
                            id="register-email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            maxlength="150"
                            placeholder="ten@example.com"
                            required>
                    </div>
                    @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary" for="register-phone">Số điện thoại <span class="text-muted fw-normal">(tùy chọn)</span></label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input
                            class="form-control @error('phone') is-invalid @enderror"
                            id="register-phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone') }}"
                            autocomplete="tel"
                            maxlength="20"
                            placeholder="0912 345 678">
                    </div>
                    @error('phone')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Passwords (2 cột trên desktop) -->
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary" for="register-password">Mật khẩu</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input
                            class="form-control has-toggle @error('password') is-invalid @enderror"
                            id="register-password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Tối thiểu 8 ký tự"
                            required>
                        <button class="btn input-group-text auth-password-toggle" type="button" onclick="togglePasswordVisibility('register-password', this)" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary" for="register-password-confirmation">Xác nhận mật khẩu</label>
                    <div class="input-group auth-input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <input
                            class="form-control has-toggle @error('password_confirmation') is-invalid @enderror"
                            id="register-password-confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Nhập lại mật khẩu"
                            required>
                        <button class="btn input-group-text auth-password-toggle" type="button" onclick="togglePasswordVisibility('register-password-confirmation', this)" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button class="btn auth-btn-primary w-100" type="submit" data-auth-submit>
                <span data-auth-submit-label>Tạo tài khoản</span>
            </button>
            <span class="visually-hidden" role="status" aria-live="polite" data-auth-status></span>
        </form>

        <!-- Divider -->
        <hr class="my-3">

        <!-- Login Link -->
        <p class="text-secondary text-center small mb-3">
            Bạn đã có tài khoản rồi?
            <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none ms-1">Đăng nhập</a>
        </p>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1 hover-primary">
                <i class="bi bi-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
@endsection
