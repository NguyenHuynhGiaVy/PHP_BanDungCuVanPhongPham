<?php

namespace App\Http\Requests\Admin;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_products') ?? false;
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $variant = $this->route('variant');

        return [
            'sku' => [
                'required',
                'string',
                'max:80',
                Rule::unique('product_variants', 'sku')->ignore($variant instanceof ProductVariant ? $variant->id : null),
            ],
            'variant_name' => ['required', 'string', 'max:150'],
            'attributes' => ['nullable', 'array'],
            'attributes.*' => ['array:name,value'],
            'attributes.*.name' => ['nullable', 'string', 'max:80', 'required_with:attributes.*.value'],
            'attributes.*.value' => ['nullable', 'string', 'max:150', 'required_with:attributes.*.name'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gte:price', 'max:9999999999'],
            'status' => ['required', 'boolean'],
        ];
    }
}
