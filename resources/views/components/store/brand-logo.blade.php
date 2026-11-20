@props(['showTagline' => true])

<a
    {{ $attributes->merge(['class' => 'store-brand text-decoration-none d-flex align-items-center']) }}
    href="{{ route('home') }}"
    aria-label="Việt Thịnh - Trang chủ">
    <img
        src="{{ asset('images/logo.png') }}"
        alt="Việt Thịnh"
        style="height: 68px; max-width: 100%; width: auto; object-fit: contain;"
        loading="eager">
</a>