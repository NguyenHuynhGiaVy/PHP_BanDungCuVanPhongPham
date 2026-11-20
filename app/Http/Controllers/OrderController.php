<?php

namespace App\Http\Controllers;

use App\Actions\CancelOrder;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('orders.index', [
            'orders' => $request->user()->orders()->latest()->paginate(10),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $order->load(['items', 'payments']);

        return view('orders.show', ['order' => $order]);
    }

    public function confirmReceived(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->order_status !== 'SHIPPED') {
            throw ValidationException::withMessages([
                'order' => 'Chỉ có thể xác nhận đã nhận khi đơn hàng đang được giao.',
            ]);
        }

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

        return back()->with('status', 'Bạn đã xác nhận nhận hàng thành công.');
    }

    public function cancel(Request $request, Order $order, CancelOrder $cancelOrder): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        if ($order->order_status !== 'PENDING') {
            throw ValidationException::withMessages([
                'order' => 'Chỉ có thể hủy đơn hàng khi đơn đang ở trạng thái Chờ xác nhận.',
            ]);
        }

        $cancelOrder->handle($order, 'Khách hàng tự hủy đơn hàng', $request->user());

        return back()->with('status', 'Đơn hàng #'.$order->order_code.' đã được hủy thành công. Tồn kho sản phẩm đã được hoàn lại.');
    }
}
