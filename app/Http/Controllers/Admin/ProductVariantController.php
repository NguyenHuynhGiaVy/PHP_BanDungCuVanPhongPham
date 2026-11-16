<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductVariantRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function create(Product $product): View
    {
        return view('admin.products.variants.form', [
            'product' => $product,
            'variant' => new ProductVariant(['status' => 1, 'price' => 0]),
        ]);
    }

    public function store(SaveProductVariantRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product): void {
            $variant = $product->variants()->create($this->variantDetails($request->validated()));
            Inventory::query()->create([
                'variant_id' => $variant->id,
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]);
        });

        return redirect()->route('admin.products.edit', $product)->with('status', 'Biến thể đã được tạo; số lượng ban đầu là 0 và có thể nhập kho ở trang tồn kho.');
    }

    public function edit(Product $product, ProductVariant $variant): View
    {
        return view('admin.products.variants.form', compact('product', 'variant'));
    }

    public function update(SaveProductVariantRequest $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        $variant->update($this->variantDetails($request->validated()));

        return redirect()->route('admin.products.edit', $product)->with('status', 'Biến thể đã được cập nhật.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        $hasHistory = $variant->inventory()->exists()
            || $variant->supplierProducts()->exists()
            || $variant->cartItems()->exists()
            || $variant->orderItems()->exists()
            || $variant->inventoryTransactions()->exists();

        if ($hasHistory) {
            $variant->update(['status' => 0]);

            return redirect()->route('admin.products.edit', $product)->with('status', 'Biến thể đã được ẩn để giữ nguyên tồn kho và lịch sử.');
        }

        $variant->delete();

        return redirect()->route('admin.products.edit', $product)->with('status', 'Biến thể đã được xóa.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function variantDetails(array $validated): array
    {
        return [
            'sku' => $validated['sku'],
            'variant_name' => $validated['variant_name'],
            'attributes' => $this->attributeValues($validated['attributes'] ?? []),
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'status' => $validated['status'],
        ];
    }

    /**
     * @param  array<int, array{name?: string|null, value?: string|null}>  $attributes
     * @return array<string, string>|null
     */
    private function attributeValues(array $attributes): ?array
    {
        $values = [];

        foreach ($attributes as $attribute) {
            $name = trim($attribute['name'] ?? '');
            $value = trim($attribute['value'] ?? '');

            if ($name !== '' && $value !== '') {
                $values[$name] = $value;
            }
        }

        return $values === [] ? null : $values;
    }
}
