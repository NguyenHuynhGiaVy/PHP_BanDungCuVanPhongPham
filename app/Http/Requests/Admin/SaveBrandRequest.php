<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveBrandRequest extends FormRequest
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
        $brand = $this->route('brand');

        return [
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:180',
                Rule::unique('brands', 'slug')->ignore($brand instanceof Brand ? $brand->id : null),
            ],
            'logo' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
        ];
    }
}
