<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    public function category(Request $request, Category $category): View
    {
        abort_unless($category->status === 1, 404);

        return $this->listing($request, $category);
    }

    public function show(string $slug): View
    {
        $product = Product::query()
            ->where('status', 1)
            ->where('slug', $slug)
            ->whereHas('category', static fn (Builder $query): Builder => $query->where('status', 1))
            ->with([
                'category:id,name,slug',
                'brand:id,name,slug,logo',
                'images' => static fn (Relation $query): Relation => $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'variants' => static fn (Relation $query): Relation => $query
                    ->where('status', 1)
                    ->orderBy('price')
                    ->orderBy('id')
                    ->with('inventory'),
                'reviews' => static fn (Relation $query): Relation => $query
                    ->where('status', 1)
                    ->with('user:id,full_name')
                    ->orderByDesc('id')
                    ->limit(3),
            ])
            ->withCount(['reviews as approved_reviews_count' => static fn (Builder $query): Builder => $query->where('status', 1)])
            ->withAvg(['reviews as approved_reviews_average' => static fn (Builder $query): Builder => $query->where('status', 1)], 'rating')
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->where('status', 1)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->whereHas('category', static fn (Builder $query): Builder => $query->where('status', 1))
            ->whereHas('variants', static fn (Builder $query): Builder => $query->where('status', 1))
            ->with($this->productCardRelations())
            ->withCount(['reviews as approved_reviews_count' => static fn (Builder $query): Builder => $query->where('status', 1)])
            ->withAvg(['reviews as approved_reviews_average' => static fn (Builder $query): Builder => $query->where('status', 1)], 'rating')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    private function listing(Request $request, ?Category $category = null): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'brand' => [
                'nullable',
                'integer',
                Rule::exists('brands', 'id')->where('status', 1),
            ],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'sort' => ['nullable', Rule::in(['newest', 'price_asc', 'price_desc', 'name'])],
        ]);

        $searchTerm = trim($filters['q'] ?? '');
        $minimumPrice = Arr::get($filters, 'min_price');
        $maximumPrice = Arr::get($filters, 'max_price');
        $brandId = Arr::get($filters, 'brand');

        $matchesSelectedVariants = static function (Builder|Relation $query) use ($minimumPrice, $maximumPrice): void {
            $query->where('status', 1);

            if ($minimumPrice !== null) {
                $query->where('price', '>=', $minimumPrice);
            }

            if ($maximumPrice !== null) {
                $query->where('price', '<=', $maximumPrice);
            }
        };

        $productsQuery = Product::query()
            ->where('status', 1)
            ->whereHas('category', static fn (Builder $query): Builder => $query->where('status', 1))
            ->whereHas('variants', $matchesSelectedVariants)
            ->when($category !== null, static fn (Builder $query): Builder => $query->where('category_id', $category->id))
            ->when($brandId !== null, static fn (Builder $query): Builder => $query->where('brand_id', $brandId))
            ->when($searchTerm !== '', static function (Builder $query) use ($searchTerm): void {
                $term = '%'.$searchTerm.'%';

                $query->where(static function (Builder $query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('brand', static fn (Builder $query): Builder => $query->where('name', 'like', $term));
                });
            })
            ->with($this->productCardRelations($matchesSelectedVariants));
        $productsQuery
            ->withCount(['reviews as approved_reviews_count' => static fn (Builder $query): Builder => $query->where('status', 1)])
            ->withAvg(['reviews as approved_reviews_average' => static fn (Builder $query): Builder => $query->where('status', 1)], 'rating');

        $sort = $filters['sort'] ?? 'newest';

        if ($sort === 'price_asc' || $sort === 'price_desc') {
            $productsQuery
                ->withMin(['variants as starting_price' => $matchesSelectedVariants], 'price')
                ->orderBy('starting_price', $sort === 'price_asc' ? 'asc' : 'desc')
                ->orderBy('id');
        } elseif ($sort === 'name') {
            $productsQuery->orderBy('name')->orderBy('id');
        } else {
            $productsQuery->orderByDesc('id');
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        $categories = Category::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $brands = Brand::query()
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $listingRoute = $request->routeIs('search') ? 'search' : 'products.index';

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'category' => $category,
            'filters' => $filters,
            'listingRoute' => $listingRoute,
        ]);
    }

    /**
     * @return array<string, string|\Closure>
     */
    private function productCardRelations(?\Closure $variantConstraint = null): array
    {
        return [
            'category:id,name,slug',
            'brand:id,name,logo',
            'images' => static fn (Relation $query): Relation => $query
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(1),
            'variants' => static function (Relation $query) use ($variantConstraint): void {
                if ($variantConstraint !== null) {
                    $variantConstraint($query);
                } else {
                    $query->where('status', 1);
                }

                $query->orderBy('price')
                    ->orderBy('id')
                    ->limit(1)
                    ->with('inventory');
            },
        ];
    }
}
