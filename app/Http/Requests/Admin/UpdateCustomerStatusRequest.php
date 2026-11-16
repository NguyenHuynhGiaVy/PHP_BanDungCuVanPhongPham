<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerStatusRequest extends FormRequest
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
            'status' => ['required', 'boolean'],
        ];
    }
}
