<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receiver_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'ward' => ['required', 'string', 'max:100'],
            'detail_address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:COD,VNPAY,MOCK'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'receiver_name.required' => 'Vui lòng nhập họ tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại người nhận.',
            'province.required' => 'Vui lòng nhập tỉnh / thành phố.',
            'district.required' => 'Vui lòng nhập quận / huyện.',
            'ward.required' => 'Vui lòng nhập phường / xã.',
            'detail_address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
