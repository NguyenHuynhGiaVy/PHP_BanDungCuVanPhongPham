<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
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
        return [
            'order_status' => ['required', Rule::in(['PROCESSING', 'SHIPPED', 'DELIVERED'])],
        ];
    }
}
