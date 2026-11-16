<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReviewStatusRequest;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', 'in:0,1,2']]);

        return view('admin.reviews.index', [
            'reviews' => Review::query()
                ->with(['user:id,full_name', 'product:id,name'])
                ->when(isset($filters['status']), static fn (Builder $query): Builder => $query->where('status', $filters['status']))
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function update(UpdateReviewStatusRequest $request, Review $review): RedirectResponse
    {
        $review->update($request->validated());

        return back()->with('status', 'Trạng thái đánh giá đã được cập nhật.');
    }
}
