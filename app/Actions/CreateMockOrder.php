<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateMockOrder
{
    /**
     * @param  array<string, mixed>  $checkoutDetails
     */
    public function handle(User $user, array $checkoutDetails): Order
    {
        return DB::transaction(function () use ($user, $checkoutDetails): Order {
            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($cart === null) {
                throw ValidationException::withMessages([
                    'cart' => 'Giỏ hàng đang trống.',
                ]);
            }

            $cartItems = $cart->items()
                ->orderBy('variant_id')
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Giỏ hàng đang trống.',
                ]);
            }

            $variants = ProductVariant::query()
                ->whereIn('id', $cartItems->pluck('variant_id'))
                ->where('status', 1)
                ->whereHas('product', static fn ($query) => $query
                    ->where('status', 1)
                    ->whereHas('category', static fn ($categoryQuery) => $categoryQuery->where('status', 1)))
                ->with('product')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $inventoryByVariant = [];
            $subtotalInCents = 0;

            foreach ($cartItems as $cartItem) {
                $variant = $variants->get($cartItem->variant_id);

                if ($variant === null) {
                    throw ValidationException::withMessages([
                        'cart' => 'Một sản phẩm trong giỏ không còn được bán. Vui lòng kiểm tra lại giỏ hàng.',
                    ]);
                }

                $inventory = Inventory::query()
                    ->where('variant_id', $variant->id)
                    ->lockForUpdate()
                    ->first();

                if ($inventory === null) {
                    throw ValidationException::withMessages([
                        'cart' => 'Tồn kho của một sản phẩm đang được cập nhật. Vui lòng thử lại sau.',
                    ]);
                }

                $availableQuantity = max(0, $inventory->quantity - $inventory->reserved_quantity);

                if ($cartItem->quantity > $availableQuantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Sản phẩm {$variant->product->name} chỉ còn {$availableQuantity} sản phẩm.",
                    ]);
                }

                $inventoryByVariant[$variant->id] = $inventory;
                $subtotalInCents += $this->amountInCents((string) $variant->price) * $cartItem->quantity;
            }

            $addressDetails = [
                'receiver_name' => $checkoutDetails['receiver_name'],
                'phone' => $checkoutDetails['phone'],
                'province' => $checkoutDetails['province'],
                'district' => $checkoutDetails['district'],
                'ward' => $checkoutDetails['ward'],
                'detail_address' => $checkoutDetails['detail_address'],
            ];
            $address = $user->addresses()->firstOrCreate($addressDetails, [
                'is_default' => ! $user->addresses()->where('is_default', true)->exists(),
            ]);

            $shippingFeeInCents = 0;
            $discountInCents = 0;
            $couponCode = trim((string) ($checkoutDetails['coupon_code'] ?? ''));
            $coupon = null;

            if ($couponCode !== '') {
                $coupon = Coupon::query()
                    ->where('code', $couponCode)
                    ->where('status', 1)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->lockForUpdate()
                    ->first();

                $canUseCoupon = $coupon !== null
                    && ((float) $coupon->min_order_value * 100 <= $subtotalInCents)
                    && ($coupon->usage_limit === null || $coupon->used_count < $coupon->usage_limit);

                if ($coupon !== null && $coupon->audience === 'private') {
                    $canUseCoupon = $canUseCoupon && $coupon->users()->whereKey($user->id)->exists();
                }

                if (! $canUseCoupon) {
                    throw ValidationException::withMessages([
                        'coupon_code' => 'Mã khuyến mãi không hợp lệ hoặc không áp dụng cho tài khoản của bạn.',
                    ]);
                }

                if ($coupon->discount_type === 'percentage') {
                    $discountInCents = (int) round($subtotalInCents * (float) $coupon->discount_value / 100);
                } else {
                    $discountInCents = $this->amountInCents((string) $coupon->discount_value);
                }

                if ($coupon->max_discount !== null) {
                    $discountInCents = min($discountInCents, $this->amountInCents((string) $coupon->max_discount));
                }

                $discountInCents = min($discountInCents, $subtotalInCents);
            }

            $totalInCents = max(0, $subtotalInCents - $discountInCents + $shippingFeeInCents);
            $shippingAddress = implode(', ', [
                $address->detail_address,
                $address->ward,
                $address->district,
                $address->province,
            ]);

            $paymentMethod = $checkoutDetails['payment_method'] ?? 'COD';
            $paymentStatus = $paymentMethod === 'MOCK' ? 'PAID' : 'PENDING';
            $orderStatus = $paymentMethod === 'MOCK' ? 'PROCESSING' : 'PENDING';

            $order = Order::query()->create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'coupon_id' => $coupon?->id,
                'order_code' => 'SS-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'subtotal' => $this->amountFromCents($subtotalInCents),
                'discount_amount' => $this->amountFromCents($discountInCents),
                'shipping_fee' => $this->amountFromCents($shippingFeeInCents),
                'total_amount' => $this->amountFromCents($totalInCents),
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'note' => $checkoutDetails['note'] ?? null,
                'receiver_name' => $address->receiver_name,
                'receiver_phone' => $address->phone,
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($cartItems as $cartItem) {
                $variant = $variants->get($cartItem->variant_id);
                $inventory = $inventoryByVariant[$variant->id];
                $lineSubtotalInCents = $this->amountInCents((string) $variant->price) * $cartItem->quantity;

                $order->items()->create([
                    'variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->variant_name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $this->amountFromCents($lineSubtotalInCents),
                ]);

                $inventory->quantity -= $cartItem->quantity;
                $inventory->save();

                InventoryTransaction::query()->create([
                    'variant_id' => $variant->id,
                    'order_id' => $order->id,
                    'transaction_type' => 'EXPORT',
                    'quantity' => $cartItem->quantity,
                    'note' => 'Giao dịch thanh toán mô phỏng.',
                ]);
            }

            $order->payments()->create([
                'transaction_code' => $paymentMethod === 'MOCK' ? 'MOCK-'.Str::upper(Str::random(16)) : null,
                'payment_method' => $paymentMethod,
                'amount' => $this->amountFromCents($totalInCents),
                'status' => $paymentStatus === 'PAID' ? 'SUCCESS' : 'PENDING',
                'paid_at' => $paymentStatus === 'PAID' ? now() : null,
            ]);

            $cart->items()->delete();

            if ($coupon !== null) {
                $coupon->increment('used_count');
                $coupon->users()->whereKey($user->id)->updateExistingPivot($user->id, [
                    'used_at' => now(),
                ]);
            }

            return $order;
        });
    }

    private function amountInCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '00');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    private function amountFromCents(int $amountInCents): string
    {
        return intdiv($amountInCents, 100).'.'.str_pad((string) ($amountInCents % 100), 2, '0', STR_PAD_LEFT);
    }
}
