<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressBookController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImageController as AdminProductImageController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', [ProductController::class, 'index'])->name('search');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/category/{category:slug}', [ProductController::class, 'category'])
    ->name('products.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticationController::class, 'createLogin'])->name('login');
    Route::post('/login', [AuthenticationController::class, 'storeLogin'])->middleware('throttle:login')->name('login.store');
    Route::get('/register', [AuthenticationController::class, 'createRegistration'])->name('register');
    Route::post('/register', [AuthenticationController::class, 'storeRegistration'])->name('register.store');
});

Route::post('/logout', [AuthenticationController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/addresses', [AddressBookController::class, 'store'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [AddressBookController::class, 'update'])
        ->whereNumber('address')
        ->name('account.addresses.update');
    Route::patch('/account/addresses/{address}/default', [AddressBookController::class, 'setDefault'])
        ->whereNumber('address')
        ->name('account.addresses.default');
    Route::delete('/account/addresses/{address}', [AddressBookController::class, 'destroy'])
        ->whereNumber('address')
        ->name('account.addresses.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
    Route::put('/cart/items/{cartItem}', [CartController::class, 'update'])->whereNumber('cartItem')->name('cart.items.update');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy'])->whereNumber('cartItem')->name('cart.items.destroy');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/products/{product}', [WishlistController::class, 'store'])->whereNumber('product')->name('wishlist.store');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->whereNumber('wishlist')->name('wishlist.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::resource('products', AdminProductController::class)->except('show');
        Route::resource('products.variants', AdminProductVariantController::class)
            ->except(['index', 'show'])
            ->scoped(['product' => 'id', 'variant' => 'id']);
        Route::patch('products/{product}/images/{image}/primary', [AdminProductImageController::class, 'setPrimary'])
            ->scopeBindings()
            ->name('products.images.primary');
        Route::delete('products/{product}/images/{image}', [AdminProductImageController::class, 'destroy'])
            ->scopeBindings()
            ->name('products.images.destroy');
        Route::resource('categories', AdminCategoryController::class)->except('show');
        Route::resource('brands', AdminBrandController::class)->except('show');
        Route::resource('suppliers', AdminSupplierController::class)->except('show');
        Route::get('inventory', [AdminInventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/{inventory}/adjustments', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::patch('customers/{customer}', [AdminCustomerController::class, 'update'])->name('customers.update');
        Route::resource('coupons', AdminCouponController::class)->except('show');
        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::put('reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    });
