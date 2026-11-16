<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#12355b">
    <meta name="description" content="Stationery Store - Văn phòng phẩm cho mọi ngày học tập và làm việc.">
    <title>@yield('title', config('app.name', 'Stationery Store'))</title>
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
        @if (session('status'))
            <div class="container pt-3">
                <div class="alert alert-success mb-0" role="status">{{ session('status') }}</div>
            </div>
        @endif
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')
</body>
</html>
