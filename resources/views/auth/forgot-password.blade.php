@extends('layouts.store')

@section('title', 'Quên mật khẩu | Stationery Store')

@section('content')
<div class="auth-page-wrapper" style="background-image: url('{{ asset('images/auth-bg.gif') }}');">
    <div class="auth-page-overlay"></div>

    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="d-inline-block mb-3 text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Việt Thịnh" style="height: 52px; width: auto; object-fit: contain;">
            </a>
            <h1 class="h4 fw-bold text-dark mb-1">Quên mật khẩu</h1>
            <p class="text-muted small mb-0">Nhập email để nhận liên kết đặt lại mật khẩu</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 small rounded-3 mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-6 align-middle"></i>
                <span>{{ session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('demo_reset_url'))
            <div class="alert alert-info py-2 px-3 small rounded-3 mb-3">
                <strong><i class="bi bi-info-circle me-1"></i> Môi trường local:</strong>
                <a href="{{ session('demo_reset_url') }}" class="d-block mt-2 text-decoration-none fw-semibold">
                    Bấm vào đây để đặt lại mật khẩu
                </a>
            </div>
        @endif

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

        <form action="{{ route('password.email') }}" method="POST" data-auth-form>
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="forgot-email">Địa chỉ Email</label>
                <div class="input-group auth-input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input
                        class="form-control @error('email') is-invalid @enderror"
                        id="forgot-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="ten@example.com"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn auth-btn-primary w-100" type="submit" data-auth-submit>
                <span data-auth-submit-label>Gửi liên kết đặt lại mật khẩu</span>
            </button>
            <span class="visually-hidden" role="status" aria-live="polite" data-auth-status></span>
        </form>

        <hr class="my-3">
        <p class="text-secondary text-center small mb-3">
            Đã nhớ mật khẩu?
            <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none ms-1">Đăng nhập</a>
        </p>
        <div class="text-center">
            <a href="{{ route('home') }}" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1 hover-primary">
                <i class="bi bi-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
