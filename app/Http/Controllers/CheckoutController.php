<?php

namespace App\Http\Controllers;

use App\Actions\CreateMockOrder;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->user()->cart()
            ->with([
                'items.variant.product',
                'items.variant.product.images' => static fn (Relation $query): Relation => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'items.variant.inventory',
            ])
            ->first();

        if ($cart === null || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Thêm sản phẩm vào giỏ hàng trước khi thanh toán.',
            ]);
        }

        $cartTotal = $cart->items->sum(fn ($item) => (float) $item->variant->price * $item->quantity);
        $defaultAddress = $request->user()->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->first();

        return view('checkout.create', [
            'cart' => $cart,
            'cartTotal' => $cartTotal,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function store(CheckoutRequest $request, CreateMockOrder $createMockOrder): RedirectResponse
    {
        $order = $createMockOrder->handle($request->user(), $request->validated());

        return redirect()
            ->route('orders.show', $order)
            ->with('status', 'Đơn hàng đã được tạo bằng phương thức thanh toán mô phỏng.');
    }
}
