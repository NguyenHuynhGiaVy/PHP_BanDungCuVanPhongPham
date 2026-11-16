<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ProductImageController extends Controller
{
    public function setPrimary(Product $product, ProductImage $image): RedirectResponse
    {
        DB::transaction(function () use ($product, $image): void {
            $product->images()->lockForUpdate()->get();
            $product->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return redirect()->route('admin.products.edit', $product)->with('status', 'Ảnh chính đã được cập nhật.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        DB::transaction(function () use ($product, $image): void {
            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $product->images()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first()
                    ?->update(['is_primary' => true]);
            }
        });

        return redirect()->route('admin.products.edit', $product)->with('status', 'Ảnh sản phẩm đã được xóa.');
    }
}
