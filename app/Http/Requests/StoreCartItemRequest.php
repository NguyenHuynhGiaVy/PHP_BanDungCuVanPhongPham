<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCartItemRequest extends FormRequest
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
            'variant_id' => ['required', 'integer', Rule::exists('product_variants', 'id')->where('status', 1)],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
            'purchase_intent' => ['nullable', Rule::in(['cart', 'checkout'])],
        ];
    }
}
