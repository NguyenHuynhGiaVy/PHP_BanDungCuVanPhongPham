<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCouponRequest;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:0,1'],
        ]);

        return view('admin.coupons.index', [
            'coupons' => Coupon::query()
                ->withCount('orders')
                ->when($filters['q'] ?? null, static fn (Builder $query, string $term): Builder => $query->where('code', 'like', '%'.$term.'%'))
                ->when(isset($filters['status']), static fn (Builder $query): Builder => $query->where('status', $filters['status']))
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['status' => 1, 'discount_type' => 'percentage', 'discount_value' => 10, 'min_order_value' => 0])]);
    }

    public function store(SaveCouponRequest $request): RedirectResponse
    {
        Coupon::query()->create($request->validated());

        return redirect()->route('admin.coupons.index')->with('status', 'Mã giảm giá đã được tạo.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(SaveCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return redirect()->route('admin.coupons.index')->with('status', 'Mã giảm giá đã được cập nhật.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        if ($coupon->orders()->exists() || $coupon->used_count > 0) {
            return back()->withErrors([
                'coupon' => 'Mã giảm giá đã được sử dụng hoặc gắn với đơn hàng. Hãy tắt trạng thái thay vì xóa.',
            ]);
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('status', 'Mã giảm giá đã được xóa.');
    }
}
