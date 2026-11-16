@extends('layouts.store')

@section('title', 'Tài khoản của tôi | Stationery Store')

@section('content')
    <div class="container py-4 py-lg-5">
        <div class="store-section-heading">
            <div>
                <span class="store-section-kicker">Xin chào, {{ $user->full_name }}</span>
                <h1 class="store-section-title">Tài khoản của tôi</h1>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-secondary btn-sm" type="submit">Đăng xuất</button>
            </form>
        </div>

        <div class="row g-4 align-items-start">
            <aside class="col-12 col-lg-3">
                @include('account.partials.navigation')
            </aside>

            <div class="col-12 col-lg-9">
                <section class="store-panel mb-4">
                    <h2 class="h5 fw-bold mb-3">Thông tin cá nhân</h2>
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('account.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="account-name">Họ và tên</label>
                                <input
                                    class="form-control @error('full_name') is-invalid @enderror"
                                    id="account-name"
                                    name="full_name"
                                    value="{{ old('full_name', $user->full_name) }}"
                                    autocomplete="name"
                                    maxlength="100"
                                    required
                                >
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="account-email">Email</label>
                                <input
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="account-email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="email"
                                    maxlength="150"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="account-phone">Số điện thoại</label>
                                <input
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="account-phone"
                                    name="phone"
                                    type="tel"
                                    value="{{ old('phone', $user->phone) }}"
                                    autocomplete="tel"
                                    maxlength="20"
                                >
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">Lưu thay đổi</button>
                            </div>
                        </div>
                    </form>
                </section>

                <section class="store-panel mb-4" id="account-password">
                    <h2 class="h5 fw-bold mb-3">Đổi mật khẩu</h2>
                    <form action="{{ route('account.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold" for="account-current-password">Mật khẩu hiện tại</label>
                                <input
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    id="account-current-password"
                                    name="current_password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                >
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="account-new-password">Mật khẩu mới</label>
                                <input
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="account-new-password"
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
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="account-password-confirmation">Xác nhận mật khẩu mới</label>
                                <input
                                    class="form-control"
                                    id="account-password-confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required
                                >
                            </div>
                            <div class="col-12">
                                <button class="btn btn-outline-primary" type="submit">Cập nhật mật khẩu</button>
                            </div>
                        </div>
                    </form>
                </section>

                <section class="store-panel mb-4">
                    <div class="store-section-heading mb-3">
                        <h2 class="h5 fw-bold mb-0">Đơn hàng gần đây</h2>
                        <a href="{{ route('orders.index') }}">Xem tất cả</a>
                    </div>
                    @forelse ($recentOrders as $order)
                        <a class="store-account-order" href="{{ route('orders.show', $order) }}">
                            <span>
                                <strong>{{ $order->order_code }}</strong>
                                <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                            </span>
                            <span class="text-end">
                                <strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }} ₫</strong>
                                <small>{{ $order->order_status }}</small>
                            </span>
                        </a>
                    @empty
                        <p class="text-secondary mb-0">Bạn chưa có đơn hàng nào.</p>
                    @endforelse
                </section>

                <section class="store-panel" id="address-book">
                    <div class="store-section-heading mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-0">Sổ địa chỉ</h2>
                            <span class="small text-secondary">Quản lý địa chỉ giao hàng của bạn</span>
                        </div>
                    </div>

                    @if ($addresses->isNotEmpty())
                        <div class="store-address-list mb-4">
                            @foreach ($addresses as $address)
                                <article class="store-address-card">
                                    <div class="store-address-heading">
                                        <span class="store-address-icon">
                                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                        </span>
                                        <div>
                                            <strong>{{ $address->receiver_name }}</strong>
                                            @if ($address->is_default)
                                                <span class="badge text-bg-primary ms-1">Mặc định</span>
                                            @endif
                                            <p class="mb-0">{{ $address->phone }}</p>
                                        </div>
                                    </div>
                                    <p class="store-address-item mb-3">
                                        {{ $address->detail_address }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->province }}
                                    </p>
                                    <div class="store-address-actions">
                                        <button
                                            class="btn btn-outline-primary btn-sm"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#edit-address-{{ $address->id }}"
                                            aria-expanded="{{ (int) old('address_id') === $address->id ? 'true' : 'false' }}"
                                            aria-controls="edit-address-{{ $address->id }}"
                                        >
                                            <i class="bi bi-pencil me-1" aria-hidden="true"></i>Sửa
                                        </button>
                                        @unless ($address->is_default)
                                            <form action="{{ route('account.addresses.default', $address) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-outline-secondary btn-sm" type="submit">Đặt mặc định</button>
                                            </form>
                                        @endunless
                                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-link btn-sm text-danger" type="submit">Xóa</button>
                                        </form>
                                    </div>

                                    <div
                                        class="collapse {{ (int) old('address_id') === $address->id ? 'show' : '' }} mt-3"
                                        id="edit-address-{{ $address->id }}"
                                    >
                                        <form
                                            action="{{ route('account.addresses.update', $address) }}"
                                            method="POST"
                                            class="store-address-form"
                                        >
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="address_id" value="{{ $address->id }}">
                                            @include('account.partials.address-fields', [
                                                'formId' => 'edit-address-'.$address->id,
                                                'address' => $address,
                                                'showDefault' => ! $address->is_default,
                                            ])
                                            <button class="btn btn-primary btn-sm" type="submit">Lưu địa chỉ</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="store-empty-state store-empty-state-compact mb-4">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <h3>Chưa có địa chỉ giao hàng</h3>
                            <p class="mb-0">Thêm địa chỉ để thuận tiện hơn khi đặt hàng.</p>
                        </div>
                    @endif

                    <div class="store-address-add">
                        <h3 class="h6 fw-bold mb-3">Thêm địa chỉ mới</h3>
                        <form action="{{ route('account.addresses.store') }}" method="POST" class="store-address-form">
                            @csrf
                            <input type="hidden" name="address_id" value="">
                            @include('account.partials.address-fields', [
                                'formId' => 'new-address',
                                'address' => null,
                                'showDefault' => $addresses->isNotEmpty(),
                            ])
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Thêm địa chỉ
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
