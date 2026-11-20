@extends('layouts.store')

@section('title', 'Đăng nhập | Việt Thịnh')

@section('content')
<div class="auth-page-wrapper" style="background-image: url('{{ asset('images/auth-bg.gif') }}');">
    <div class="auth-page-overlay"></div>

    <div class="auth-card">
        <!-- Logo & Title -->
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Việt Thịnh" style="height: 52px; width: auto; object-fit: contain;">
            </a>
            <h1 class="h4 fw-bold text-dark mb-1">Đăng nhập tài khoản</h1>
            <p class="text-muted small mb-0">Chào mừng bạn quay lại với Văn Phòng Phẩm Việt Thịnh</p>
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

        <form action="{{ route('login.store') }}" method="POST" data-auth-form>
            @csrf
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="login-email">Địa chỉ Email</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        class="form-control @error('email') is-invalid @enderror"
                        id="login-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        placeholder="ten@example.com"
                        required
                        autofocus>
                </div>
                @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="login-password">Mật khẩu</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        class="form-control has-toggle @error('password') is-invalid @enderror"
                        id="login-password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        required>
                    <button class="btn input-group-text auth-password-toggle" type="button" onclick="togglePasswordVisibility('login-password', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember & Forgot -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check mb-0">
                    <input class="form-check-input" id="remember-me" name="remember" type="checkbox" value="1">
                    <label class="form-check-label small text-secondary user-select-none" for="remember-me">Ghi nhớ đăng nhập</label>
                </div>
                <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none fw-medium">
                    Quên mật khẩu?
                </a>
            </div>

            <!-- Submit Button -->
            <button class="btn auth-btn-primary w-100" type="submit" data-auth-submit>
                <span data-auth-submit-label>Đăng nhập</span>
            </button>
            <span class="visually-hidden" role="status" aria-live="polite" data-auth-status></span>
        </form>

        <!-- Divider -->
        <div class="text-center my-3 text-muted position-relative">
            <hr class="my-3">
        </div>

        <!-- Register Link -->
        <p class="text-secondary text-center small mb-3">
            Bạn chưa có tài khoản?
            <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none ms-1">Đăng ký ngay</a>
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
