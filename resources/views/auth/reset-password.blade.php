@extends('layouts.store')

@section('title', 'Đặt lại mật khẩu | Stationery Store')

@section('content')
<div class="auth-page-wrapper" style="background-image: url('{{ asset('images/auth-bg.gif') }}');">
    <div class="auth-page-overlay"></div>

    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Việt Thịnh" style="height: 52px; width: auto; object-fit: contain;">
            </a>
            <h1 class="h4 fw-bold text-dark mb-1">Đặt lại mật khẩu mới</h1>
            <p class="text-muted small mb-0">Tạo mật khẩu mới có tối thiểu 8 ký tự</p>
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

        <form action="{{ route('password.update') }}" method="POST" data-auth-form>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="reset-email">Email tài khoản</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        class="form-control @error('email') is-invalid @enderror"
                        id="reset-email"
                        name="email"
                        type="email"
                        value="{{ old('email', $email) }}"
                        required
                        readonly
                    >
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="reset-password">Mật khẩu mới</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input
                        class="form-control has-toggle @error('password') is-invalid @enderror"
                        id="reset-password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Tối thiểu 8 ký tự"
                        required
                        autofocus
                    >
                    <button class="btn input-group-text auth-password-toggle" type="button" onclick="togglePasswordVisibility('reset-password', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary" for="reset-password-confirm">Xác nhận mật khẩu mới</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                    <input
                        class="form-control has-toggle"
                        id="reset-password-confirm"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        placeholder="Nhập lại mật khẩu mới"
                        required
                    >
                    <button class="btn input-group-text auth-password-toggle" type="button" onclick="togglePasswordVisibility('reset-password-confirm', this)" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button class="btn auth-btn-primary w-100" type="submit" data-auth-submit>
                <span data-auth-submit-label>Cập nhật mật khẩu mới</span>
            </button>
            <span class="visually-hidden" role="status" aria-live="polite" data-auth-status></span>
        </form>

        <hr class="my-3">
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1 hover-primary">
                <i class="bi bi-arrow-left"></i> Hủy bỏ và quay lại đăng nhập
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
