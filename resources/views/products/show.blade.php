@extends('layouts.store')

@section('title', $product->name.' | Việt Thịnh')

@section('content')
    @php
        $selectedVariant = $product->variants->firstWhere('id', (int) old('variant_id'))
            ?? $product->variants->first();
        $selectedInventory = $selectedVariant?->inventory;
        $selectedQuantity = $selectedInventory
            ? max(0, $selectedInventory->quantity - $selectedInventory->reserved_quantity)
            : null;
        $primaryImage = $product->images->first();
        $isFavorited = !is_null($userWishlistId);
    @endphp

    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav class="store-breadcrumb" aria-label="Điều hướng đường dẫn">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('products.category', $product->category) }}">{{ $product->category->name }}</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- Alert thêm vào yêu thích / xóa khỏi yêu thích --}}
        @if (session('wishlist_status') === 'added')
            <div class="alert alert-success d-flex align-items-center rounded-3 shadow-sm border-0 mb-3 p-3" role="alert">
                <i class="bi bi-heart-fill text-danger fs-5 me-2"></i>
                <div class="small fw-semibold">Đã thêm sản phẩm vào danh sách yêu thích!</div>
            </div>
        @elseif (session('wishlist_status') === 'removed')
            <div class="alert alert-secondary d-flex align-items-center rounded-3 shadow-sm border-0 mb-3 p-3" role="alert">
                <i class="bi bi-heart fs-5 me-2"></i>
                <div class="small fw-semibold">Đã xóa sản phẩm khỏi danh sách yêu thích.</div>
            </div>
        @endif

        {{-- Alert thêm vào giỏ hàng --}}
        @if (session('status'))
            <div class="alert alert-success d-flex align-items-center rounded-3 shadow-sm border-0 mb-3 p-3" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                <div class="small fw-semibold">{{ session('status') }}</div>
            </div>
        @endif

        {{-- Alert đánh giá --}}
        @if (session('review_success'))
            <div class="alert alert-success d-flex align-items-center rounded-3 shadow-sm border-0 mb-3 p-3" role="alert">
                <i class="bi bi-star-fill text-warning fs-5 me-2"></i>
                <div class="small fw-semibold">{{ session('review_success') }}</div>
            </div>
        @endif
        @if (session('review_error'))
            <div class="alert alert-warning d-flex align-items-center rounded-3 shadow-sm border-0 mb-3 p-3" role="alert">
                <i class="bi bi-exclamation-circle-fill text-warning fs-5 me-2"></i>
                <div class="small fw-semibold">{{ session('review_error') }}</div>
            </div>
        @endif

        <!-- Product Main Detail Card -->
        <article class="store-product-detail">
            <div class="row g-4 g-lg-5">
                <!-- CỘT TRÁI: GALLERY ẢNH -->
                <div class="col-12 col-lg-5 col-xl-5">
                    <div class="store-detail-gallery" data-product-gallery>
                        <div class="store-detail-image">
                            <!-- Nút Yêu thích nổi góc ảnh (toggle) -->
                            <form action="{{ route('wishlist.store', $product) }}" method="POST" class="store-detail-wishlist-float">
                                @csrf
                                <button class="btn p-0 border-0 d-flex align-items-center justify-content-center w-100 h-100" type="submit" title="{{ $isFavorited ? 'Bỏ yêu thích' : 'Thêm vào yêu thích' }}" aria-label="Yêu thích">
                                    <i class="bi {{ $isFavorited ? 'bi-heart-fill text-danger' : 'bi-heart text-secondary' }}"></i>
                                </button>
                            </form>

                            @if ($primaryImage)
                                <img
                                    src="{{ str_starts_with($primaryImage->image_url, 'http') ? $primaryImage->image_url : asset($primaryImage->image_url) }}"
                                    alt="{{ $product->name }}"
                                    data-gallery-main
                                    data-product-image
                                    onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.removeAttribute('hidden');"
                                >
                                <span class="store-product-image-fallback" data-product-image-fallback hidden>
                                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                                </span>
                            @else
                                <span class="store-product-image-fallback">
                                    <i class="bi bi-box-seam" aria-hidden="true"></i>
                                </span>
                            @endif
                        </div>

                        @if ($product->images->count() > 1)
                            <div class="store-detail-thumbnails" aria-label="Ảnh sản phẩm">
                                @foreach ($product->images as $image)
                                    <button
                                        class="store-detail-thumbnail {{ $loop->first ? 'active' : '' }}"
                                        type="button"
                                        data-gallery-thumbnail
                                        data-image-src="{{ str_starts_with($image->image_url, 'http') ? $image->image_url : asset($image->image_url) }}"
                                        data-image-alt="{{ $product->name }} - ảnh {{ $loop->iteration }}"
                                        aria-label="Xem ảnh sản phẩm {{ $loop->iteration }}"
                                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        <img
                                            src="{{ str_starts_with($image->image_url, 'http') ? $image->image_url : asset($image->image_url) }}"
                                            alt=""
                                            loading="lazy"
                                            data-product-image
                                            onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.removeAttribute('hidden');"
                                        >
                                        <span class="store-product-image-fallback" hidden>
                                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- CỘT PHẢI: THÔNG TIN SẢN PHẨM -->
                <div class="col-12 col-lg-7 col-xl-7">
                    <!-- Brand & Kicker -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if ($product->brand)
                            <span class="store-detail-brand">{{ $product->brand->name }}</span>
                        @endif
                        <span class="text-muted small">
                            Danh mục: <a href="{{ route('products.category', $product->category) }}" class="text-decoration-none fw-medium">{{ $product->category->name }}</a>
                        </span>
                    </div>

                    <!-- Tiêu đề sản phẩm (Vừa vặn, tinh tế) -->
                    <h1 class="store-detail-title">{{ $product->name }}</h1>

                    <!-- Rating & SKU bar -->
                    <div class="d-flex flex-wrap align-items-center gap-3 store-detail-sku mb-3">
                        <div class="d-flex align-items-center gap-1 text-warning">
                            @php
                                $approvedReviewCount = (int) ($product->approved_reviews_count ?? 0);
                                $avgRating = $approvedReviewCount > 0 ? (float) ($product->approved_reviews_average ?? 0) : 0;
                            @endphp
                            @for ($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $avgRating >= $star - 0.5 ? 'bi-star-fill' : 'bi-star' }} fs-6"></i>
                            @endfor
                            <span class="text-dark fw-bold ms-1" style="font-size: 0.85rem;">{{ number_format((float) $avgRating, 1, ',', '.') }}</span>
                            <span class="text-muted" style="font-size: 0.8rem;">({{ $product->approved_reviews_count ?? 0 }} đánh giá)</span>
                        </div>
                        <span class="text-muted">|</span>
                        <div>
                            Mã SKU: <strong class="text-dark" data-variant-sku>{{ $selectedVariant?->sku ?? 'Đang cập nhật' }}</strong>
                        </div>
                    </div>

                    <!-- Khung giá sản phẩm -->
                    <div class="store-detail-price-box">
                        @if ($selectedVariant)
                            <span class="store-detail-price" data-variant-price>{{ number_format((float) $selectedVariant->price, 0, ',', '.') }} ₫</span>
                            <del
                                data-variant-compare
                                @if (!$selectedVariant->compare_price || $selectedVariant->compare_price <= $selectedVariant->price)
                                    hidden
                                @endif
                            >
                                {{ $selectedVariant->compare_price
                                    ? number_format((float) $selectedVariant->compare_price, 0, ',', '.').' ₫'
                                    : '' }}
                            </del>
                            @if ($selectedVariant->compare_price && $selectedVariant->compare_price > $selectedVariant->price)
                                @php
                                    $discountPercent = round((($selectedVariant->compare_price - $selectedVariant->price) / $selectedVariant->compare_price) * 100);
                                @endphp
                                <span class="store-detail-discount-badge">-{{ $discountPercent }}%</span>
                            @endif
                        @else
                            <span class="store-detail-price" data-variant-price>Giá đang cập nhật</span>
                            <del data-variant-compare hidden></del>
                        @endif
                    </div>

                    <!-- Form Mua Hàng -->
                    <form action="{{ route('cart.items.store') }}" method="POST" data-product-cart-form>
                        @csrf
                        
                        <!-- Phân loại / Biến thể -->
                        @if ($product->variants->isNotEmpty())
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary" for="product-variant">Phân loại sản phẩm</label>
                                <select
                                    class="form-select @error('variant_id') is-invalid @enderror"
                                    id="product-variant"
                                    name="variant_id"
                                    required
                                    @error('variant_id') aria-describedby="product-variant-error" @enderror
                                    @error('variant_id') aria-invalid="true" @enderror
                                    data-variant-selector
                                    style="max-width: 380px;"
                                >
                                    @foreach ($product->variants as $variant)
                                        @php
                                            $inventory = $variant->inventory;
                                            $availableQuantity = $inventory
                                                ? max(0, $inventory->quantity - $inventory->reserved_quantity)
                                                : null;
                                        @endphp
                                        <option
                                            value="{{ $variant->id }}"
                                            data-price="{{ $variant->price }}"
                                            data-compare-price="{{ $variant->compare_price }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-available="{{ $availableQuantity ?? '' }}"
                                            @selected($selectedVariant?->id === $variant->id)
                                        >
                                            {{ $variant->variant_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('variant_id')
                                    <div class="invalid-feedback d-block" id="product-variant-error">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <p class="store-detail-notice mb-3">
                                Giá và phân loại sản phẩm đang được cập nhật.
                            </p>
                        @endif

                        <!-- Tình trạng kho hàng -->
                        <div class="store-detail-stock" data-variant-stock aria-live="polite">
                            @if ($selectedQuantity === null)
                                <span class="text-secondary"><i class="bi bi-info-circle me-1"></i> Đang cập nhật tồn kho</span>
                            @elseif ($selectedQuantity > 0)
                                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Còn hàng ({{ $selectedQuantity }} sản phẩm có sẵn)</span>
                            @else
                                <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Tạm hết hàng</span>
                            @endif
                        </div>

                        <!-- Cụm chọn Số lượng & Nút hành động -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary" for="product-quantity">Số lượng</label>
                            <div class="store-detail-purchase">
                                <!-- Bộ đếm số lượng tinh chỉnh [-] [input] [+] -->
                                <div class="store-detail-qty-wrapper">
                                    <button type="button" class="store-detail-qty-btn" onclick="adjustProductQuantity(-1)">−</button>
                                    <input
                                        class="store-detail-qty-input no-spinner @error('quantity') is-invalid @enderror"
                                        id="product-quantity"
                                        type="number"
                                        value="{{ old('quantity', 1) }}"
                                        min="1"
                                        name="quantity"
                                        readonly
                                        style="cursor: default; user-select: none;"
                                        @error('quantity') aria-describedby="product-quantity-error" @enderror
                                        @error('quantity') aria-invalid="true" @enderror
                                        @if ($selectedQuantity !== null)
                                            max="{{ max(1, $selectedQuantity) }}"
                                        @endif
                                        data-variant-quantity
                                    >
                                    <button type="button" class="store-detail-qty-btn" onclick="adjustProductQuantity(1)">+</button>
                                </div>

                                <!-- Nút Thêm vào giỏ hàng -->
                                <button
                                    class="btn store-btn-cart"
                                    type="submit"
                                    name="purchase_intent"
                                    value="cart"
                                    @disabled($selectedQuantity === null || $selectedQuantity < 1 || $product->variants->isEmpty())
                                    data-variant-submit
                                    data-pending-label="Đang thêm..."
                                >
                                    <i class="bi bi-cart-plus fs-5"></i>
                                    <span data-cart-submit-label>Thêm vào giỏ</span>
                                </button>

                                <!-- Nút Mua ngay -->
                                <button
                                    class="btn store-btn-buy"
                                    type="submit"
                                    name="purchase_intent"
                                    value="checkout"
                                    @disabled($selectedQuantity === null || $selectedQuantity < 1 || $product->variants->isEmpty())
                                    data-variant-submit
                                    data-pending-label="Đang chuyển..."
                                >
                                    <span data-cart-submit-label>Mua ngay</span>
                                </button>
                            </div>
                            @error('quantity')
                                <span class="invalid-feedback d-block" id="product-quantity-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </article>

        <!-- MÔ TẢ CHI TIẾT SẢN PHẨM -->
        @if ($product->description)
            <section class="store-detail-section-card">
                <div class="mb-3 pb-2 border-bottom">
                    <h2 class="h5 fw-bold text-dark mb-0">Mô tả sản phẩm</h2>
                </div>
                <div class="store-description-content">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </section>
        @endif

        <!-- ĐÁNH GIÁ SẢN PHẨM -->
        <section class="store-detail-section-card">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                <h2 class="h5 fw-bold text-dark mb-0">Đánh giá sản phẩm</h2>
                @if ($product->approved_reviews_count > 0)
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                        <i class="bi bi-star-fill text-warning me-1"></i>
                        {{ number_format((float) $product->approved_reviews_average, 1, ',', '.') }} / 5.0 ({{ $product->approved_reviews_count }} nhận xét)
                    </span>
                @endif
            </div>

            @if ($product->reviews->isNotEmpty())
                <div class="store-review-list d-flex flex-column gap-3 mb-4">
                    @foreach ($product->reviews as $review)
                        <article class="p-3 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                        {{ mb_substr($review->user->full_name ?? 'K', 0, 1) }}
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $review->user->full_name }}</strong>
                                        <span class="text-warning" style="font-size: 0.85rem;">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <i class="bi {{ $s <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                            @endfor
                                        </span>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                            </div>
                            @if ($review->comment)
                                <p class="mb-0 text-secondary ps-4" style="font-size: 0.9rem;">{{ $review->comment }}</p>
                            @endif
                            @if ($review->admin_reply)
                                <div class="ms-4 mt-3 p-3 rounded-3 border border-primary-subtle bg-white">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-shop text-primary"></i>
                                        <strong class="small text-dark">Phản hồi từ cửa hàng</strong>
                                    </div>
                                    <p class="mb-0 text-secondary small">{{ $review->admin_reply }}</p>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted mb-4">
                    <i class="bi bi-chat-square-dots fs-2 text-secondary d-block mb-2"></i>
                    <p class="mb-0 small">Sản phẩm này chưa có đánh giá nào. Hãy là người đầu tiên nhận xét nhé!</p>
                </div>
            @endif

            {{-- FORM GỬI ĐÁNH GIÁ --}}
            @auth
                @if (!$userReview)
                    @if ($eligibleReviewOrderItemId)
                    <div class="card border-0 bg-light rounded-3 p-4">
                        <h3 class="h6 fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-pencil-square text-primary"></i> Viết đánh giá của bạn
                        </h3>
                        @if ($errors->has('rating') || $errors->has('comment'))
                            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                                @foreach ($errors->only(['rating', 'comment']) as $e)
                                    <div>{{ $e }}</div>
                                @endforeach
                            </div>
                        @endif
                        <form action="{{ route('reviews.store', $product) }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_item_id" value="{{ $eligibleReviewOrderItemId }}">
                            {{-- Chọn số sao --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary d-block">Đánh giá sao <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-1" id="star-rating-row">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <label class="star-label" style="cursor: pointer; font-size: 1.6rem; color: #d1d5db; transition: color 0.15s;" data-star="{{ $s }}">
                                            <i class="bi bi-star-fill"></i>
                                            <input type="radio" name="rating" value="{{ $s }}" class="visually-hidden" @checked(old('rating') == $s) required>
                                        </label>
                                    @endfor
                                    <span class="ms-2 small text-muted" id="star-label-text">Chọn số sao</span>
                                </div>
                            </div>
                            {{-- Nhận xét --}}
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary" for="review-comment">Nhận xét (không bắt buộc)</label>
                                <textarea
                                    class="form-control"
                                    id="review-comment"
                                    name="comment"
                                    rows="3"
                                    maxlength="1000"
                                    placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."
                                >{{ old('comment') }}</textarea>
                            </div>
                            <button class="btn btn-primary px-4 fw-semibold" type="submit">
                                <i class="bi bi-send me-1"></i> Gửi đánh giá
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="alert alert-info d-flex align-items-center gap-2 rounded-3 border-0 small">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Bạn chỉ có thể đánh giá sản phẩm trong đơn hàng đã giao thành công.</span>
                    </div>
                    @endif
                @else
                    <div class="alert alert-info d-flex align-items-center gap-2 rounded-3 border-0 small">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Bạn đã gửi đánh giá cho sản phẩm này.
                            @if ($userReview->status == 0)
                                Đánh giá đang <strong>chờ duyệt</strong>.
                            @else
                                Đánh giá đã được <strong>hiển thị</strong>.
                            @endif
                        </span>
                    </div>
                @endif
            @else
                <div class="p-3 bg-light rounded-3 border text-center small text-muted">
                    <i class="bi bi-person-lock me-1"></i>
                    <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Đăng nhập</a> để viết đánh giá sản phẩm.
                </div>
            @endauth
        </section>
    </div>

    <!-- SẢN PHẨM CÙNG DANH MỤC / LIÊN QUAN -->
    @if ($relatedProducts->isNotEmpty())
        <section class="store-section store-section-tinted mt-5">
            <div class="container">
                <div class="store-section-heading">
                    <div>
                        <span class="store-section-kicker">Cùng danh mục</span>
                        <h2 class="store-section-title">Sản phẩm liên quan</h2>
                    </div>
                    <a class="store-section-link" href="{{ route('products.category', $product->category) }}">
                        Xem danh mục
                        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-sm-3 row-cols-xl-4 g-3 g-lg-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col">
                            <x-store.product-card :product="$relatedProduct" />
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <script>
        function adjustProductQuantity(delta) {
            const input = document.getElementById('product-quantity');
            if (!input) return;
            const current = parseInt(input.value) || 1;
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || 9999;
            const next = current + delta;
            if (next >= min && next <= max) {
                input.value = next;
            }
        }

        // Star rating UI
        (function () {
            const starLabels = document.querySelectorAll('#star-rating-row .star-label');
            const labelText = document.getElementById('star-label-text');
            const starNames = ['', 'Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Rất tốt'];

            function paint(upTo) {
                starLabels.forEach((lbl, idx) => {
                    lbl.style.color = idx < upTo ? '#f59e0b' : '#d1d5db';
                });
                if (labelText) labelText.textContent = upTo > 0 ? starNames[upTo] : 'Chọn số sao';
            }

            // Init from old() value if any
            const checkedRadio = document.querySelector('#star-rating-row input[type=radio]:checked');
            if (checkedRadio) paint(parseInt(checkedRadio.value));

            starLabels.forEach((lbl, idx) => {
                lbl.addEventListener('mouseenter', () => paint(idx + 1));
                lbl.addEventListener('mouseleave', () => {
                    const sel = document.querySelector('#star-rating-row input[type=radio]:checked');
                    paint(sel ? parseInt(sel.value) : 0);
                });
                lbl.addEventListener('click', () => paint(idx + 1));
            });
        })();
    </script>
@endsection
