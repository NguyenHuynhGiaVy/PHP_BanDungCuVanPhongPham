<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Inventory;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->user()->cart()
            ->with([
                'items.variant.product.images' => static fn (Relation $query): Relation => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'items.variant.inventory',
            ])
            ->first();

        return view('cart.index', ['cart' => $cart]);
    }

    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated): void {
            $cart = $request->user()->cart()->firstOrCreate([], ['status' => 1]);
            $cart = $cart->newQuery()->whereKey($cart->id)->lockForUpdate()->firstOrFail();

            $variant = ProductVariant::query()
                ->whereKey($validated['variant_id'])
                ->where('status', 1)
                ->whereHas('product', static fn ($query) => $query
                    ->where('status', 1)
                    ->whereHas('category', static fn ($categoryQuery) => $categoryQuery->where('status', 1)))
                ->lockForUpdate()
                ->firstOrFail();
            $inventory = $this->inventoryFor($variant);
            $cartItem = $cart->items()->where('variant_id', $variant->id)->first();
            $newQuantity = ($cartItem?->quantity ?? 0) + $validated['quantity'];
            $this->ensureAvailableQuantity($variant, $inventory, $newQuantity);

            if ($cartItem === null) {
                $cart->items()->create([
                    'variant_id' => $variant->id,
                    'quantity' => $newQuantity,
                ]);
            } else {
                $cartItem->update(['quantity' => $newQuantity]);
            }
        });

        $destination = ($validated['purchase_intent'] ?? 'cart') === 'checkout'
            ? 'checkout.create'
            : 'cart.index';

        return redirect()->route($destination)->with('status', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }

    public function update(UpdateCartItemRequest $request, int $cartItem): RedirectResponse
    {
        DB::transaction(function () use ($request, $cartItem): void {
            $cart = $request->user()->cart()
                ->lockForUpdate()
                ->firstOrFail();
            $item = $cart->items()
                ->with('variant')
                ->lockForUpdate()
                ->findOrFail($cartItem);
            $inventory = $this->inventoryFor($item->variant);
            $this->ensureAvailableQuantity($item->variant, $inventory, $request->validated('quantity'));

            $item->update(['quantity' => $request->validated('quantity')]);
        });

        return back()->with('status', 'Số lượng sản phẩm đã được cập nhật.');
    }

    public function destroy(Request $request, int $cartItem): RedirectResponse
    {
        DB::transaction(function () use ($request, $cartItem): void {
            $cart = $request->user()->cart()
                ->lockForUpdate()
                ->firstOrFail();
            $cart->items()->whereKey($cartItem)->firstOrFail()->delete();
        });

        return back()->with('status', 'Sản phẩm đã được xóa khỏi giỏ hàng.');
    }

    private function inventoryFor(ProductVariant $variant): Inventory
    {
        $inventory = Inventory::query()
            ->where('variant_id', $variant->id)
            ->lockForUpdate()
            ->first();

        if ($inventory === null) {
            throw ValidationException::withMessages([
                'quantity' => 'Tồn kho của sản phẩm đang được cập nhật. Vui lòng thử lại sau.',
            ]);
        }

        return $inventory;
    }

    private function ensureAvailableQuantity(ProductVariant $variant, Inventory $inventory, int $quantity): void
    {
        $availableQuantity = max(0, $inventory->quantity - $inventory->reserved_quantity);

        if ($quantity > $availableQuantity) {
            throw ValidationException::withMessages([
                'quantity' => "Sản phẩm {$variant->variant_name} chỉ còn {$availableQuantity} sản phẩm.",
            ]);
        }
    }
}
