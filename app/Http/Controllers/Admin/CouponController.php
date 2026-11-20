<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCouponRequest;
use App\Models\Coupon;
use App\Models\User;
use App\Notifications\CouponAssignedNotification;
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
            'customers' => User::query()
                ->whereHas('role', static fn (Builder $query): Builder => $query->where('name', 'CUSTOMER'))
                ->where('status', 1)
                ->orderBy('full_name')
                ->get(['id', 'full_name', 'email', 'phone']),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.form', [
            'coupon' => new Coupon(['status' => 1, 'audience' => 'public', 'discount_type' => 'percentage', 'discount_value' => 10, 'min_order_value' => 0]),
            'customers' => User::query()->whereHas('role', static fn (Builder $query): Builder => $query->where('name', 'CUSTOMER'))->where('status', 1)->orderBy('full_name')->get(['id', 'full_name', 'email']),
        ]);
    }

    public function store(SaveCouponRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $recipientIds = $validated['recipient_ids'] ?? [];
        unset($validated['recipient_ids']);

        $coupon = Coupon::query()->create($validated);
        $this->assignToRecipients($coupon, $recipientIds);

        return redirect()->route('admin.coupons.index')->with('status', 'Mã giảm giá đã được tạo.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', [
            'coupon' => $coupon->load('users:id,full_name,email'),
            'customers' => User::query()->whereHas('role', static fn (Builder $query): Builder => $query->where('name', 'CUSTOMER'))->where('status', 1)->orderBy('full_name')->get(['id', 'full_name', 'email']),
        ]);
    }

    public function update(SaveCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validated();
        $recipientIds = $validated['recipient_ids'] ?? [];
        unset($validated['recipient_ids']);

        $coupon->update($validated);
        $this->assignToRecipients($coupon, $recipientIds);

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

    public function send(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validate([
            'recipient_ids' => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $recipientIds = array_values(array_unique(array_map('intval', $validated['recipient_ids'])));

        $recipients = User::query()
            ->whereKey($recipientIds)
            ->where('status', 1)
            ->whereHas('role', static fn (Builder $query): Builder => $query->where('name', 'CUSTOMER'))
            ->get();

        if ($recipients->isEmpty()) {
            return back()->withErrors([
                'recipient_ids' => 'Vui lòng chọn ít nhất một khách hàng đang hoạt động.',
            ]);
        }

        $coupon->audience = 'private';
        $coupon->save();
        $coupon->users()->syncWithoutDetaching($recipients->modelKeys());

        foreach ($recipients as $recipient) {
            $recipient->notify(new CouponAssignedNotification($coupon));
        }

        return back()->with('status', 'Đã gửi mã '.$coupon->code.' cho '.$recipients->count().' khách hàng.');
    }

    private function assignToRecipients(Coupon $coupon, array $recipientIds): void
    {
        if ($coupon->audience !== 'private') {
            $coupon->users()->detach();

            return;
        }

        $recipientIds = array_values(array_unique(array_map('intval', $recipientIds)));
        $coupon->users()->sync($recipientIds);

        foreach (User::query()->whereKey($recipientIds)->get() as $customer) {
            $customer->notify(new CouponAssignedNotification($coupon));
        }
    }
}
