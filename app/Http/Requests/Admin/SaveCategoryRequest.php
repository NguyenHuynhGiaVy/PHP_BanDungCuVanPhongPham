<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SaveCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_categories') ?? false;
    }

    /**
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                Rule::notIn($category instanceof Category ? [$category->id] : []),
            ],
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:180',
                Rule::unique('categories', 'slug')->ignore($category instanceof Category ? $category->id : null),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $category = $this->route('category');
                $parentId = $this->input('parent_id');

                if (! $category instanceof Category || $parentId === null || $parentId === '' || $validator->errors()->has('parent_id')) {
                    return;
                }

                $descendantIds = [$category->id];
                $pendingIds = $descendantIds;

                while ($pendingIds !== []) {
                    $childIds = Category::query()
                        ->whereIn('parent_id', $pendingIds)
                        ->pluck('id')
                        ->map(static fn ($id): int => (int) $id)
                        ->all();
                    $pendingIds = array_values(array_diff($childIds, $descendantIds));
                    $descendantIds = array_merge($descendantIds, $pendingIds);
                }

                if (in_array((int) $parentId, $descendantIds, true)) {
                    $validator->errors()->add('parent_id', 'Không thể chọn danh mục con làm danh mục cha.');
                }
            },
        ];
    }
}
