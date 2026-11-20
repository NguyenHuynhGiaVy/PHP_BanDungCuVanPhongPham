<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveBrandRequest;
use App\Models\Brand;
use App\Services\CloudinaryStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(private readonly CloudinaryStorage $cloudinaryStorage) {}

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
        $validated = $request->validated();
        $validated['logo'] = $this->storeLogo($request);
        Brand::query()->create($validated);

        return redirect()->route('admin.brands.index')->with('status', 'Thương hiệu đã được tạo.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.form', ['brand' => $brand]);
    }

    public function update(SaveBrandRequest $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validated();
        $newLogo = $this->storeLogo($request);

        if ($newLogo !== null) {
            $this->deleteLogo($brand->logo);
            $validated['logo'] = $newLogo;
        } else {
            unset($validated['logo']);
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')->with('status', 'Thương hiệu đã được cập nhật.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->withErrors([
                'brand' => 'Thương hiệu đang gắn với sản phẩm. Hãy ẩn thương hiệu thay vì xóa.',
            ]);
        }

        $this->deleteLogo($brand->logo);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('status', 'Thương hiệu đã được xóa.');
    }

    private function storeLogo(SaveBrandRequest $request): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }
        $result = $this->cloudinaryStorage->upload(
            $request->file('logo'),
            'brands',
        );

        return $result['secure_url'];
    }

    private function deleteLogo(?string $logo): void
    {
        $this->cloudinaryStorage->delete($logo);
    }
}
