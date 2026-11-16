<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdjustInventoryRequest;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120']]);

        return view('admin.inventory.index', [
            'inventories' => Inventory::query()
                ->with('variant.product:id,name')
                ->when($filters['q'] ?? null, static fn (Builder $query, string $term): Builder => $query
                    ->whereHas('variant', static fn (Builder $variantQuery): Builder => $variantQuery
                        ->where('sku', 'like', '%'.$term.'%')
                        ->orWhereHas('product', static fn (Builder $productQuery): Builder => $productQuery->where('name', 'like', '%'.$term.'%'))))
                ->orderBy('quantity')
                ->paginate(20)
                ->withQueryString(),
            'suppliers' => Supplier::query()->where('status', 1)->orderBy('name')->get(['id', 'name']),
            'transactions' => InventoryTransaction::query()
                ->with(['variant.product:id,name', 'supplier:id,name'])
                ->latest('id')
                ->limit(20)
                ->get(),
        ]);
    }

    public function adjust(AdjustInventoryRequest $request, Inventory $inventory): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($inventory, $validated): void {
            $lockedInventory = Inventory::query()->lockForUpdate()->findOrFail($inventory->id);
            $quantity = (int) $validated['quantity'];

            if ($validated['transaction_type'] === 'EXPORT') {
                $availableQuantity = max(0, $lockedInventory->quantity - $lockedInventory->reserved_quantity);

                if ($quantity > $availableQuantity) {
                    throw ValidationException::withMessages([
                        'quantity' => "Chỉ còn {$availableQuantity} sản phẩm khả dụng; không thể xuất số lượng yêu cầu.",
                    ]);
                }

                $lockedInventory->quantity -= $quantity;
            } else {
                if ($lockedInventory->quantity + $quantity > 4294967295) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Tổng số lượng vượt quá giới hạn lưu trữ.',
                    ]);
                }

                $lockedInventory->quantity += $quantity;
            }

            $lockedInventory->save();
            InventoryTransaction::query()->create([
                'variant_id' => $lockedInventory->variant_id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'transaction_type' => $validated['transaction_type'],
                'quantity' => $quantity,
                'note' => $validated['note'] ?? null,
            ]);
        });

        return back()->with('status', 'Tồn kho đã được cập nhật và ghi nhận vào lịch sử.');
    }
}
