<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CancelOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'order_status' => ['nullable', 'in:PENDING,PROCESSING,SHIPPED,DELIVERED,CANCELLED'],
        ]);

        return view('admin.orders.index', [
            'orders' => Order::query()
                ->with('user:id,full_name,email')
                ->when($filters['q'] ?? null, static fn (Builder $query, string $term): Builder => $query
                    ->where(static fn (Builder $orderQuery): Builder => $orderQuery
                        ->where('order_code', 'like', '%'.$term.'%')
                        ->orWhere('receiver_name', 'like', '%'.$term.'%')
                        ->orWhere('receiver_phone', 'like', '%'.$term.'%')))
                ->when($filters['order_status'] ?? null, static fn (Builder $query, string $status): Builder => $query->where('order_status', $status))
                ->latest('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user:id,full_name,email,phone', 'items', 'payments']);

        return view('admin.orders.show', [
            'order' => $order,
            'nextStatuses' => $this->nextStatuses($order),
        ]);
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order,
        CancelOrder $cancelOrder,
    ): RedirectResponse {
        $status = $request->validated('order_status');

        if (! in_array($status, $this->nextStatuses($order), true)) {
            throw ValidationException::withMessages([
                'order_status' => 'Không thể chuyển đơn hàng sang trạng thái đã chọn.',
            ]);
        }

        if ($status === 'CANCELLED') {
            $cancelOrder->handle($order, 'Quản trị viên chuyển trạng thái sang Đã hủy', $request->user());

            return redirect()->route('admin.orders.show', $order)->with('status', 'Đơn hàng đã được hủy và tồn kho đã được hoàn lại.');
        }

        if ($status === 'DELIVERED') {
            $updateData = ['order_status' => 'DELIVERED'];

            if ($order->payment_method === 'COD') {
                $updateData['payment_status'] = 'PAID';
            }

            $order->update($updateData);

            if ($order->payment_method === 'COD') {
                $order->payments()->latest('id')->first()?->update([
                    'status' => 'SUCCESS',
                    'paid_at' => now(),
                ]);
            }

            return redirect()->route('admin.orders.show', $order)->with('status', 'Đơn hàng đã được cập nhật sang trạng thái Đã giao hàng (DELIVERED).');
        }

        $order->update(['order_status' => $status]);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Trạng thái đơn hàng đã được cập nhật.');
    }

    /**
     * @return list<string>
     */
    private function nextStatuses(Order $order): array
    {
        return match ($order->order_status) {
            'PENDING' => $order->payment_status === 'PAID'
                || $order->payment_method === 'COD'
                ? ['PROCESSING', 'CANCELLED']
                : ['CANCELLED'],
            'PROCESSING' => $order->payment_status === 'PAID'
                || $order->payment_method === 'COD'
                ? ['SHIPPED', 'CANCELLED']
                : ['CANCELLED'],
            'SHIPPED' => ['DELIVERED', 'CANCELLED'],
            default => [],
        };
    }
}
