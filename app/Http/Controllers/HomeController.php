<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $activeProduct = static fn (Builder $query): Builder => $query->where('status', 1);
        $approvedReviews = static fn (Builder $query): Builder => $query->where('status', 1);

        $productCardRelations = [
            'brand:id,name,logo',
            'category:id,name,slug',
            'images' => static fn (Relation $query): Relation => $query
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(1),
            'variants' => static fn (Relation $query): Relation => $query
                ->where('status', 1)
                ->orderBy('price')
                ->limit(1)
                ->with('inventory'),
        ];
        $discountedProductCardRelations = array_merge($productCardRelations, [
            'variants' => static fn (Relation $query): Relation => $query
                ->where('status', 1)
                ->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price')
                ->orderByRaw('(compare_price - price) DESC')
                ->orderBy('price')
                ->orderBy('id')
                ->limit(1)
                ->with('inventory'),
        ]);

        $categories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'slug']);

        $newProducts = Product::query()
            ->where('status', 1)
            ->with($productCardRelations)
            ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
            ->withAvg(['reviews as approved_reviews_average' => $approvedReviews], 'rating')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $paidProcessingOrderItems = DB::table('order_items')
            ->join('product_variants', 'product_variants.id', '=', 'order_items.variant_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('SUM(order_items.quantity)')
            ->whereColumn('product_variants.product_id', 'products.id')
            ->where('orders.payment_status', 'PAID')
            ->where('orders.order_status', 'PROCESSING');

        $bestSellingProducts = Product::query()
            ->where('status', 1)
            ->whereHas('category', static fn (Builder $query): Builder => $query->where('status', 1))
            ->whereHas('variants.orderItems.order', static fn (Builder $query): Builder => $query
                ->where('payment_status', 'PAID')
                ->where('order_status', 'PROCESSING'))
            ->with($productCardRelations)
            ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
            ->withAvg(['reviews as approved_reviews_average' => $approvedReviews], 'rating')
            ->addSelect(['sold_quantity' => $paidProcessingOrderItems])
            ->orderByDesc('sold_quantity')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $promotionalProducts = Product::query()
            ->where('status', 1)
            ->whereHas('category', static fn (Builder $query): Builder => $query->where('status', 1))
            ->whereHas('variants', static fn (Builder $query): Builder => $query
                ->where('status', 1)
                ->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price'))
            ->with($discountedProductCardRelations)
            ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
            ->withAvg(['reviews as approved_reviews_average' => $approvedReviews], 'rating')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $productCategories = Category::query()
            ->where('status', 1)
            ->whereHas('products', $activeProduct)
            ->withCount(['products as active_products_count' => $activeProduct])
            ->with([
                'products' => static fn (Relation $query): Relation => $query
                    ->where('status', 1)
                    ->with($productCardRelations)
                    ->withCount(['reviews as approved_reviews_count' => $approvedReviews])
                    ->withAvg(['reviews as approved_reviews_average' => $approvedReviews], 'rating')
                    ->orderByDesc('id')
                    ->limit(4),
            ])
            ->orderByDesc('active_products_count')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'slug']);

        $brands = Brand::query()
            ->where('status', 1)
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'slug', 'logo']);

        return view('home', [
            'categories' => $categories,
            'newProducts' => $newProducts,
            'bestSellingProducts' => $bestSellingProducts,
            'promotionalProducts' => $promotionalProducts,
            'productCategories' => $productCategories,
            'brands' => $brands,
        ]);
    }
}
