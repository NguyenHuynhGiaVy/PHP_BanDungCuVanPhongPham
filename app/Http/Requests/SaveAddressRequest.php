<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
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
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
