<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlists = $request->user()->wishlists()
            ->whereHas('product', static fn (Builder $query): Builder => $query
                ->where('status', 1)
                ->whereHas('category', static fn (Builder $categoryQuery): Builder => $categoryQuery->where('status', 1)))
            ->with([
                'product' => static function (Relation $query): void {
                    $query->with([
                        'category',
                        'brand',
                        'images' => static fn (Relation $imageQuery): Relation => $imageQuery
                            ->orderByDesc('is_primary')
                            ->orderBy('sort_order')
                            ->orderBy('id')
                            ->limit(1),
                        'variants' => static fn (Relation $variantQuery): Relation => $variantQuery
                            ->where('status', 1)
                            ->orderBy('price')
                            ->orderBy('id')
                            ->limit(1)
                            ->with('inventory'),
                    ])
                        ->withCount(['reviews as approved_reviews_count' => static fn (Builder $reviewQuery): Builder => $reviewQuery->where('status', 1)])
                        ->withAvg(['reviews as approved_reviews_average' => static fn (Builder $reviewQuery): Builder => $reviewQuery->where('status', 1)], 'rating');
                },
            ])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('wishlist.index', ['wishlists' => $wishlists]);
    }

    /**
     * Toggle: nếu đã yêu thích thì xóa, chưa thì thêm vào.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === 1 && $product->category()->where('status', 1)->exists(), 404);

        $existing = $request->user()->wishlists()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('wishlist_status', 'removed');
        }

        $request->user()->wishlists()->create(['product_id' => $product->id]);

        return back()->with('wishlist_status', 'added');
    }

    public function destroy(Request $request, Wishlist $wishlist): RedirectResponse
    {
        $request->user()->wishlists()
            ->whereKey($wishlist->id)
            ->firstOrFail()
            ->delete();

        return back()->with('wishlist_status', 'removed');
    }
}
