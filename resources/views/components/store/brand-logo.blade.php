<a
    {{ $attributes->merge(['class' => 'store-brand']) }}
    href="{{ route('home') }}"
    aria-label="Stationery Store - Trang chủ"
>
    <span class="store-brand-mark" aria-hidden="true">
        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 10.5C15.2 8.6 20.1 9.2 24 12.2V39C20.1 36 15.2 35.4 10 37.3V10.5Z" fill="currentColor" fill-opacity=".92"/>
            <path d="M38 10.5C32.8 8.6 27.9 9.2 24 12.2V39C27.9 36 32.8 35.4 38 37.3V10.5Z" fill="currentColor" fill-opacity=".62"/>
            <path d="M15 17.5C17.3 17.2 19.1 17.6 21 18.6M15 22.5C17.3 22.2 19.1 22.6 21 23.6M27 18.6C28.9 17.6 30.7 17.2 33 17.5M27 23.6C28.9 22.6 30.7 22.2 33 22.5" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M33.8 8.2L38.8 13.2L29.6 22.4L25.9 23.1L26.6 19.4L33.8 8.2Z" fill="#F39A28" stroke="white" stroke-width="1.4" stroke-linejoin="round"/>
            <path d="M25.9 23.1L29.6 22.4" stroke="#12355B" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
    </span>
    <span class="store-brand-copy">
        <span class="store-brand-name">Stationery <span>Store</span></span>
        <span class="store-brand-tagline">Văn phòng phẩm cho mọi ngày</span>
    </span>
</a>
