<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120']]);

        return view('admin.brands.index', [
            'brands' => Brand::query()
                ->when($filters['q'] ?? null, static fn ($query, string $term) => $query->where('name', 'like', '%'.$term.'%'))
                ->withCount('products')
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.brands.form', ['brand' => new Brand(['status' => 1])]);
    }

    public function store(SaveBrandRequest $request): RedirectResponse
    {
        Brand::query()->create($request->validated());

        return redirect()->route('admin.brands.index')->with('status', 'Thương hiệu đã được tạo.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.form', ['brand' => $brand]);
    }

    public function update(SaveBrandRequest $request, Brand $brand): RedirectResponse
    {
        $brand->update($request->validated());

        return redirect()->route('admin.brands.index')->with('status', 'Thương hiệu đã được cập nhật.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->withErrors([
                'brand' => 'Thương hiệu đang gắn với sản phẩm. Hãy ẩn thương hiệu thay vì xóa.',
            ]);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('status', 'Thương hiệu đã được xóa.');
    }
}
