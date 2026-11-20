<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === 1 && $product->category()->where('status', 1)->exists(), 404);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'order_item_id' => ['required', 'integer'],
        ]);

        $user = $request->user();

        $orderItem = OrderItem::query()
            ->whereKey($validated['order_item_id'])
            ->whereHas('order', static fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('order_status', 'DELIVERED'))
            ->whereHas('variant', static fn ($query) => $query->where('product_id', $product->id))
            ->whereDoesntHave('review')
            ->first();

        abort_unless($orderItem, 403);

        // Mỗi dòng đơn hàng chỉ được đánh giá một lần.
        $existing = Review::where('user_id', $user->id)
            ->where('order_item_id', $orderItem->id)
            ->first();

        if ($existing) {
            return back()->with('review_error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_item_id' => $orderItem->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 0, // Chờ duyệt
        ]);

        return back()->with('review_success', 'Cảm ơn bạn đã đánh giá! Nhận xét của bạn đang chờ duyệt.');
    }
}
