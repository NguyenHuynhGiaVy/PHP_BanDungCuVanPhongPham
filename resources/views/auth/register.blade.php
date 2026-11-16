@extends('layouts.store')

@section('title', 'Đăng ký | Stationery Store')

@section('content')
    <div class="container py-5">
        <div class="store-form-card mx-auto">
            <span class="store-section-kicker">Tạo tài khoản mua sắm</span>
            <h1 class="store-section-title h3 mb-4">Đăng ký</h1>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" data-auth-form>
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="register-name">Họ và tên</label>
                    <input
                        class="form-control @error('full_name') is-invalid @enderror"
                        id="register-name"
                        name="full_name"
                        type="text"
                        value="{{ old('full_name') }}"
                        autocomplete="name"
                        maxlength="100"
                        required
                        autofocus
                    >
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="register-email">Email</label>
                    <input
                        class="form-control @error('email') is-invalid @enderror"
                        id="register-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        maxlength="150"
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="register-phone">Số điện thoại (không bắt buộc)</label>
                    <input
                        class="form-control @error('phone') is-invalid @enderror"
                        id="register-phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        maxlength="20"
                    >
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="register-password">Mật khẩu</label>
                    <input
                        class="form-control @error('password') is-invalid @enderror"
                        id="register-password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold" for="register-password-confirmation">Nhập lại mật khẩu</label>
                    <input
                        class="form-control @error('password_confirmation') is-invalid @enderror"
                        id="register-password-confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button class="btn btn-primary w-100" type="submit" data-auth-submit>
                    <span data-auth-submit-label>Tạo tài khoản</span>
                </button>
                <span class="visually-hidden" role="status" aria-live="polite" data-auth-status></span>
            </form>

            <p class="text-secondary text-center small mt-4 mb-0">
                Đã có tài khoản?
                <a href="{{ route('login') }}">Đăng nhập</a>
            </p>
        </div>
    </div>
@endsection
