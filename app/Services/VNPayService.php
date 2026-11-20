<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;

class VNPayService
{
    /**
     * Tạo URL chuyển hướng sang cổng thanh toán VNPay
     */
    public function createPaymentUrl(Order $order): string
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code', '9Q4FIFFF');
        $vnp_HashSecret = config('services.vnpay.hash_secret', 'U3H8IWEOPB9JEE38PX4IVPW5AI6ARCNA');
        $vnp_Url = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_ReturnUrl = config('services.vnpay.return_url', route('checkout.vnpay.return'));

        $vnp_TxnRef = $order->id.'_'.time();
        $vnp_OrderInfo = 'Thanh toan don hang '.$order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int) round(((float) $order->total_amount) * 100);
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $this->normalizeIpAddress(request()->ip());

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_ReturnUrl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        ksort($inputData);
        $hashData = [];
        $queryData = [];

        foreach ($inputData as $key => $value) {
            $encodedKey = urlencode((string) $key);
            $encodedValue = urlencode((string) $value);
            $hashData[] = $encodedKey.'='.$encodedValue;
            $queryData[] = $encodedKey.'='.$encodedValue;
        }

        $hashData = implode('&', $hashData);
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $paymentUrl = rtrim((string) $vnp_Url, '?&').
            '?'.implode('&', $queryData).
            '&vnp_SecureHashType=HMACSHA512'.
            '&vnp_SecureHash='.$vnp_SecureHash;

        return $paymentUrl;
    }

    /**
     * Xác thực chữ ký phản hồi từ VNPay
     */
    public function validateSignature(Request $request): bool
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret', 'U3H8IWEOPB9JEE38PX4IVPW5AI6ARCNA');
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = [];

        foreach ($inputData as $key => $value) {
            $hashData[] = urlencode((string) $key).'='.urlencode((string) $value);
        }

        $hashData = implode('&', $hashData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        return hash_equals($secureHash, (string) $vnp_SecureHash);
    }

    private function normalizeIpAddress(?string $ipAddress): string
    {
        $ipAddress = trim((string) $ipAddress);

        if ($ipAddress === '' || $ipAddress === '::1') {
            return '127.0.0.1';
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ipAddress;
        }

        return '127.0.0.1';
    }
}
