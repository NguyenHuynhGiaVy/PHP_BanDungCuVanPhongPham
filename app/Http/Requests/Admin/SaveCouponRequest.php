<?php

namespace App\Http\Requests\Admin;

use App\Models\Coupon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role()->where('name', 'ADMIN')->exists() ?? false;
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $coupon = $this->route('coupon');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon instanceof Coupon ? $coupon->id : null),
            ],
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999',
                Rule::when($this->input('discount_type') === 'percentage', ['max:100']),
            ],
            'min_order_value' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'max_discount' => ['nullable', 'numeric', 'gt:0', 'max:9999999999'],
            'usage_limit' => [
                'nullable',
                'integer',
                'min:'.($coupon instanceof Coupon ? $coupon->used_count : 0),
                'max:4294967295',
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::upper(trim((string) $this->input('code')))]);
        }
    }
}
