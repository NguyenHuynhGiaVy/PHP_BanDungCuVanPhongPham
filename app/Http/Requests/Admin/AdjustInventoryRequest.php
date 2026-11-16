<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustInventoryRequest extends FormRequest
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
            'transaction_type' => ['required', Rule::in(['IMPORT', 'EXPORT'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'supplier_id' => [
                'nullable',
                'integer',
                Rule::exists('suppliers', 'id'),
                Rule::prohibitedIf($this->input('transaction_type') !== 'IMPORT'),
            ],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
