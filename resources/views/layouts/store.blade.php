<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#12355b">
    <meta name="description" content="Văn Phòng Việt - Văn phòng phẩm và thiết bị văn phòng chính hãng. Giải pháp toàn diện cho doanh nghiệp và văn phòng hiện đại.">
    <title>@yield('title', config('app.name', 'Văn Phòng Việt'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    @include('layouts.partials.topbar')
    <div class="store-sticky-header">
        @include('layouts.partials.header')
        @include('layouts.partials.navigation')
    </div>

    <main class="store-page-content">
        @if (session('logout_status'))
            <div class="container pt-3">
                <div class="alert alert-secondary alert-dismissible fade show d-flex align-items-center rounded-3 shadow-sm border-0 mb-0 py-3 px-3" role="status">
                    <i class="bi bi-box-arrow-right text-secondary fs-5 me-2"></i>
                    <div class="small fw-semibold">{{ session('logout_status') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Đóng"></button>
                </div>
            </div>
        @endif
        @if (session('status') && ! request()->routeIs('orders.show') && ! request()->routeIs('password.request') && ! request()->routeIs('password.reset'))
        <div class="container pt-3">
            <div class="alert alert-success d-flex align-items-center rounded mb-0" role="status">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('status') }}</div>
            </div>
        </div>
        @endif
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')
</body>

</html>