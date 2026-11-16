<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120']]);

        return view('admin.categories.index', [
            'categories' => Category::query()
                ->when($filters['q'] ?? null, static fn ($query, string $term) => $query->where('name', 'like', '%'.$term.'%'))
                ->with('parent:id,name')
                ->withCount(['products', 'children'])
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(['status' => 1]),
            'parentCategories' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(SaveCategoryRequest $request): RedirectResponse
    {
        Category::query()->create($request->validated());

        return redirect()->route('admin.categories.index')->with('status', 'Danh mục đã được tạo.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parentCategories' => Category::query()
                ->whereKeyNot($category->id)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(SaveCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')->with('status', 'Danh mục đã được cập nhật.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists() || $category->children()->exists()) {
            return back()->withErrors([
                'category' => 'Danh mục đang có sản phẩm hoặc danh mục con. Hãy ẩn danh mục thay vì xóa.',
            ]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Danh mục đã được xóa.');
    }
}
