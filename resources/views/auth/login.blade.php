@extends('layouts.store')

@section('title', 'Đăng nhập | Stationery Store')

@section('content')
    <div class="container py-5">
        <div class="store-form-card mx-auto">
            <span class="store-section-kicker">Chào mừng trở lại</span>
            <h1 class="store-section-title h3 mb-4">Đăng nhập</h1>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST" data-auth-form>
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="login-email">Email</label>
                    <input
                        class="form-control @error('email') is-invalid @enderror"
                        id="login-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        autofocus
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="login-password">Mật khẩu</label>
                    <input
                        class="form-control @error('password') is-invalid @enderror"
                        id="login-password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" id="remember-me" name="remember" type="checkbox" value="1">
                    <label class="form-check-label" for="remember-me">Ghi nhớ đăng nhập</label>
                </div>
                <button class="btn btn-primary w-100" type="submit" data-auth-submit>
                    <span data-auth-submit-label>Đăng nhập</span>
                </button>
                <span class="visually-hidden" role="status" aria-live="polite" data-auth-status></span>
            </form>

            <p class="text-secondary text-center small mt-4 mb-0">
                Chưa có tài khoản?
                <a href="{{ route('register') }}">Đăng ký ngay</a>
            </p>
        </div>
    </div>
@endsection
