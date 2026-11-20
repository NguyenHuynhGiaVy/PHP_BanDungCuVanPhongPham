<?php

namespace App\Actions;

use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelOrder
{
    /**
     * Cancel an order, refund inventory stock, update payment status, and restore any coupon used.
     *
     * @throws ValidationException
     */
    public function handle(Order $order, ?string $reason = null, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $reason): Order {
            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (in_array($lockedOrder->order_status, ['CANCELLED', 'DELIVERED'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'Đơn hàng không thể hủy ở trạng thái '.$lockedOrder->order_status.'.',
                ]);
            }

            $lockedOrder->order_status = 'CANCELLED';
            if ($lockedOrder->payment_status !== 'PAID') {
                $lockedOrder->payment_status = 'FAILED';
            }
            $lockedOrder->save();

            // Mark any pending payments as FAILED
            $lockedOrder->payments()
                ->where('status', 'PENDING')
                ->update([
                    'status' => 'FAILED',
                ]);

            // Refund inventory stock and record inventory transaction
            $items = $lockedOrder->items()->get();
            foreach ($items as $item) {
                $inventory = Inventory::query()
                    ->where('variant_id', $item->variant_id)
                    ->lockForUpdate()
                    ->first();

                if ($inventory !== null) {
                    $inventory->quantity += $item->quantity;
                    $inventory->save();

                    InventoryTransaction::query()->create([
                        'variant_id' => $item->variant_id,
                        'order_id' => $lockedOrder->id,
                        'transaction_type' => 'IMPORT',
                        'quantity' => $item->quantity,
                        'note' => 'Hoàn kho do hủy đơn #'.$lockedOrder->order_code.($reason ? ' ('.$reason.')' : ''),
                    ]);
                }
            }

            // Restore coupon if used
            if ($lockedOrder->coupon_id !== null) {
                $coupon = Coupon::query()
                    ->whereKey($lockedOrder->coupon_id)
                    ->lockForUpdate()
                    ->first();

                if ($coupon !== null && $coupon->used_count > 0) {
                    $coupon->decrement('used_count');
                    $coupon->users()->whereKey($lockedOrder->user_id)->updateExistingPivot($lockedOrder->user_id, [
                        'used_at' => null,
                    ]);
                }
            }

            return $lockedOrder;
        });
    }
}
