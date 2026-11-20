<?php

namespace App\Http\Controllers;

use App\Actions\CancelOrder;
use App\Actions\CreateMockOrder;
use App\Http\Requests\CheckoutRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\VNPayService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->user()->cart()
            ->with([
                'items.variant.product',
                'items.variant.product.images' => static fn (Relation $query): Relation => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'items.variant.inventory',
            ])
            ->first();

        if ($cart === null || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Thêm sản phẩm vào giỏ hàng trước khi thanh toán.',
            ]);
        }

        $cartTotal = $cart->items->sum(fn ($item) => (float) $item->variant->price * $item->quantity);
        $defaultAddress = $request->user()->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->first();

        return view('checkout.create', [
            'cart' => $cart,
            'cartTotal' => $cartTotal,
            'defaultAddress' => $defaultAddress,
            'availableCoupons' => Coupon::query()
                ->where('status', 1)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(static fn ($query) => $query
                    ->where('audience', 'public')
                    ->orWhere(static fn ($privateQuery) => $privateQuery
                        ->where('audience', 'private')
                        ->whereHas('users', static fn ($userQuery) => $userQuery->whereKey($request->user()->id))))
                ->orderBy('end_date')
                ->get(['id', 'code', 'discount_type', 'discount_value', 'audience']),
        ]);
    }

    public function store(
        CheckoutRequest $request,
        CreateMockOrder $createMockOrder,
        VNPayService $vnPayService,
    ): RedirectResponse {
        $order = $createMockOrder->handle($request->user(), $request->validated());

        if ($order->payment_method === 'VNPAY') {
            $paymentUrl = $vnPayService->createPaymentUrl($order);

            return redirect()->away($paymentUrl);
        }

        $message = $order->payment_method === 'COD'
            ? 'Đơn hàng đã được tạo thành công! Bạn sẽ thanh toán khi nhận hàng.'
            : 'Đơn hàng đã được tạo bằng phương thức thanh toán mô phỏng.';

        return redirect()
            ->route('orders.show', $order)
            ->with('status', $message);
    }

    public function vnpayReturn(
        Request $request,
        VNPayService $vnPayService,
        CancelOrder $cancelOrder,
    ): RedirectResponse {
        $isValidSignature = $vnPayService->validateSignature($request);
        $responseCode = $request->input('vnp_ResponseCode');
        $txnRef = $request->input('vnp_TxnRef', '');

        $orderId = (int) explode('_', $txnRef)[0];
        $order = Order::query()->find($orderId);

        if (! $order) {
            return redirect()->route('orders.index')->withErrors([
                'order' => 'Không tìm thấy thông tin đơn hàng thanh toán.',
            ]);
        }

        if ($isValidSignature && $responseCode === '00') {
            $order->update([
                'payment_status' => 'PAID',
                'order_status' => 'PROCESSING',
            ]);

            $order->payments()->latest('id')->first()?->update([
                'status' => 'SUCCESS',
                'transaction_code' => $request->input('vnp_TransactionNo', 'VNPAY-'.$txnRef),
                'paid_at' => now(),
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('status', 'Thanh toán đơn hàng qua VNPay thành công! Chúng tôi đang chuẩn bị hàng cho bạn.');
        }

        $order->payments()->latest('id')->first()?->update([
            'status' => 'FAILED',
        ]);

        if ($order->order_status !== 'CANCELLED') {
            $cancelOrder->handle(
                $order,
                'Thanh toán VNPay không thành công hoặc bị hủy (Mã phản hồi: '.($responseCode ?? 'Không xác định').')'
            );
        }

        return redirect()
            ->route('orders.show', $order)
            ->withErrors([
                'payment' => 'Giao dịch qua VNPay không thành công hoặc đã bị hủy (Mã phản hồi: '.($responseCode ?? 'Không xác định').'). Đơn hàng đã được chuyển sang trạng thái HỦY và tồn kho đã được hoàn lại.',
            ]);
    }
}
