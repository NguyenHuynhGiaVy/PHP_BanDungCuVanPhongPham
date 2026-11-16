<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:0,1'],
        ]);

        return view('admin.products.index', [
            'products' => Product::query()
                ->with(['category:id,name', 'brand:id,name'])
                ->withCount(['variants', 'reviews', 'wishlists'])
                ->when($filters['q'] ?? null, static fn (Builder $query, string $term): Builder => $query
                    ->where(static fn (Builder $productQuery): Builder => $productQuery
                        ->where('name', 'like', '%'.$term.'%')
                        ->orWhere('slug', 'like', '%'.$term.'%')))
                ->when(isset($filters['status']), static fn (Builder $query): Builder => $query->where('status', $filters['status']))
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['status' => 1]),
            'primaryVariant' => new ProductVariant(['status' => 1, 'price' => 0]),
            ...$this->formOptions(),
        ]);
    }

    public function store(SaveProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $product = Product::query()->create([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            foreach ($validated['variants'] as $variantInput) {
                $variant = $product->variants()->create([
                    ...$this->variantDetails([
                        ...$variantInput,
                        'status' => $validated['status'],
                    ]),
                ]);
                Inventory::query()->create([
                    'variant_id' => $variant->id,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]);
            }

            if (($validated['image_url'] ?? '') !== '') {
                $product->images()->create([
                    'image_url' => $validated['image_url'],
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            }

        });

        return redirect()->route('admin.products.index')->with('status', 'Sản phẩm và các biến thể đã được tạo.');
    }

    public function edit(Product $product): View
    {
        $product->load([
            'variants' => static fn (Relation $query): Relation => $query->orderBy('id')->with('inventory'),
            'images' => static fn (Relation $query): Relation => $query->orderByDesc('is_primary')->orderBy('sort_order'),
        ]);

        return view('admin.products.form', [
            'product' => $product,
            'primaryVariant' => $product->variants->first() ?? new ProductVariant(['status' => 1, 'price' => 0]),
            ...$this->formOptions(),
        ]);
    }

    public function update(SaveProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($product, $validated): void {
            $product->update([
                'category_id' => $validated['category_id'],
                'brand_id' => $validated['brand_id'] ?? null,
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $variant = $product->variants()->orderBy('id')->lockForUpdate()->first();
            $variantDetails = $this->variantDetails($validated);

            if ($variant === null) {
                $variant = $product->variants()->create($variantDetails);
                Inventory::query()->create([
                    'variant_id' => $variant->id,
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]);
            } else {
                $variant->update($variantDetails);
            }

            if (($validated['image_url'] ?? '') !== '') {
                $product->images()->create([
                    'image_url' => $validated['image_url'],
                    'is_primary' => ! $product->images()->where('is_primary', true)->exists(),
                    'sort_order' => (int) $product->images()->max('sort_order') + 1,
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('status', 'Thông tin sản phẩm đã được cập nhật.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->variants()->exists() || $product->reviews()->exists() || $product->wishlists()->exists()) {
            return back()->withErrors([
                'product' => 'Sản phẩm đã có biến thể, đánh giá hoặc yêu thích. Hãy ẩn sản phẩm để lưu dữ liệu liên quan.',
            ]);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Sản phẩm đã được xóa.');
    }

    /**
     * @return array{categories: Collection, brands: Collection}
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'brands' => Brand::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function variantDetails(array $validated): array
    {
        return [
            'sku' => $validated['sku'],
            'variant_name' => $validated['variant_name'],
            'attributes' => $this->attributeValues($validated['attributes'] ?? []),
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'status' => $validated['status'],
        ];
    }

    /**
     * @param  array<int, array{name?: string|null, value?: string|null}>  $attributes
     * @return array<string, string>|null
     */
    private function attributeValues(array $attributes): ?array
    {
        $values = [];

        foreach ($attributes as $attribute) {
            $name = trim($attribute['name'] ?? '');
            $value = trim($attribute['value'] ?? '');

            if ($name !== '' && $value !== '') {
                $values[$name] = $value;
            }
        }

        return $values === [] ? null : $values;
    }
}
