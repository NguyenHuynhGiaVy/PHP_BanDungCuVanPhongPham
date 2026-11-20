<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveProductRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->route('product') instanceof Product || $this->input('slug_is_auto', '1') !== '1') {
            return;
        }

        $name = $this->input('name');

        if (! is_string($name) || trim($name) === '') {
            return;
        }

        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 2;

        while ($slug !== '' && Product::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        $this->merge(['slug' => $slug]);
    }

    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_products') ?? false;
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $primaryVariant = $product instanceof Product
            ? $product->variants()->orderBy('id')->first()
            : null;

        $productRules = [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'brand_id' => ['nullable', 'integer', Rule::exists('brands', 'id')],
            'name' => ['required', 'string', 'max:200'],
            'slug' => [
                'required',
                'string',
                'max:220',
                Rule::unique('products', 'slug')->ignore($product instanceof Product ? $product->id : null),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'image_url' => ['nullable', 'string', 'max:255'],
        ];

        if ($product instanceof Product) {
            return [
                ...$productRules,
                'sku' => [
                    'required',
                    'string',
                    'max:80',
                    Rule::unique('product_variants', 'sku')->ignore($primaryVariant?->id),
                ],
                'variant_name' => ['required', 'string', 'max:150'],
                'attributes' => ['nullable', 'array'],
                'attributes.*' => ['array:name,value'],
                'attributes.*.name' => ['nullable', 'string', 'max:80', 'required_with:attributes.*.value'],
                'attributes.*.value' => ['nullable', 'string', 'max:150', 'required_with:attributes.*.name'],
                'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
                'compare_price' => ['nullable', 'numeric', 'min:0', 'gte:price', 'max:9999999999'],
            ];
        }

        return [
            ...$productRules,
            'variants' => ['required', 'array', 'min:1'],
            'variants.*' => ['required', 'array:sku,variant_name,attributes,price,compare_price'],
            'variants.*.sku' => [
                'required',
                'string',
                'max:80',
                'distinct',
                Rule::unique('product_variants', 'sku'),
            ],
            'variants.*.variant_name' => ['required', 'string', 'max:150'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.attributes.*' => ['array:name,value'],
            'variants.*.attributes.*.name' => ['nullable', 'string', 'max:80', 'required_with:variants.*.attributes.*.value'],
            'variants.*.attributes.*.value' => ['nullable', 'string', 'max:150', 'required_with:variants.*.attributes.*.name'],
            'variants.*.price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'variants.*.compare_price' => ['nullable', 'numeric', 'min:0', 'gte:variants.*.price', 'max:9999999999'],
        ];
    }
}
