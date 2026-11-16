@extends('layouts.store')

@section('title', 'Sản phẩm yêu thích | Stationery Store')

@section('content')
    <div class="container py-4 py-lg-5">
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.show') }}">Tài khoản</a></li>
                <li class="breadcrumb-item active" aria-current="page">Yêu thích</li>
            </ol>
        </nav>
        <div class="store-section-heading">
            <div>
                <span class="store-section-kicker">Lưu lại để xem sau</span>
                <h1 class="store-section-title">Sản phẩm yêu thích</h1>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>
            <div class="col-12 col-lg-9">
                @if ($wishlists->isNotEmpty())
                    <div class="row row-cols-2 row-cols-sm-2 row-cols-xl-3 g-3 g-lg-4">
                        @foreach ($wishlists as $wishlist)
                            <div class="col">
                                <div class="store-wishlist-card">
                                    <x-store.product-card :product="$wishlist->product" />
                                    <form action="{{ route('wishlist.destroy', $wishlist) }}" method="POST" class="p-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-secondary btn-sm w-100" type="submit">
                                            <i class="bi bi-heartbreak me-1" aria-hidden="true"></i>
                                            Xóa khỏi yêu thích
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="store-pagination mt-4">{{ $wishlists->links() }}</div>
                @else
                    <div class="store-empty-state">
                        <span class="store-empty-icon"><i class="bi bi-heart" aria-hidden="true"></i></span>
                        <h2>Danh sách yêu thích đang trống</h2>
                        <p class="mb-0">Lưu sản phẩm bạn quan tâm để dễ dàng tìm lại.</p>
                        <a class="btn btn-primary mt-2" href="{{ route('products.index') }}">Khám phá sản phẩm</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
