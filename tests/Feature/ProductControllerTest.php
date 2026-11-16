<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_listing_filters_by_brand_and_price_and_sorts_matching_variants(): void
    {
        $categoryId = $this->createCategory('Vở và sổ', 'vo-va-so');
        $brandId = $this->createBrand('Thiên Long', 'thien-long');
        $otherBrandId = $this->createBrand('Hồng Hà', 'hong-ha');

        $lowProductId = $this->createProduct('Sổ giá thấp', 'so-gia-thap', $categoryId, $brandId);
        $this->createVariant($lowProductId, 'SO-THAP', 'Bìa mềm', 8000);

        $firstMatchingProductId = $this->createProduct('Sổ giá vừa', 'so-gia-vua', $categoryId, $brandId);
        $this->createVariant($firstMatchingProductId, 'SO-VUA', 'Bìa cứng', 15000);

        $secondMatchingProductId = $this->createProduct('Sổ giá cao hơn', 'so-gia-cao-hon', $categoryId, $brandId);
        $this->createVariant($secondMatchingProductId, 'SO-CAO', 'Bìa da', 19000);

        $otherBrandProductId = $this->createProduct('Sổ thương hiệu khác', 'so-thuong-hieu-khac', $categoryId, $otherBrandId);
        $this->createVariant($otherBrandProductId, 'SO-KHAC', 'Bìa vải', 16000);

        $response = $this->get(route('products.index', [
            'brand' => $brandId,
            'min_price' => 10000,
            'max_price' => 20000,
            'sort' => 'price_asc',
        ]));

        $response->assertOk();
        $response->assertSeeInOrder(['Sổ giá vừa', 'Sổ giá cao hơn']);
        $response->assertDontSee('Sổ giá thấp');
        $response->assertDontSee('Sổ thương hiệu khác');

        $this->get(route('products.index', ['brand' => $brandId, 'sort' => 'price_desc']))
            ->assertOk()
            ->assertSeeInOrder(['Sổ giá cao hơn', 'Sổ giá vừa', 'Sổ giá thấp']);

        $this->get(route('products.index', ['brand' => $brandId, 'sort' => 'name']))
            ->assertOk()
            ->assertSeeInOrder(['Sổ giá cao hơn', 'Sổ giá thấp', 'Sổ giá vừa']);
    }

    public function test_search_paginates_matching_products_and_preserves_filters(): void
    {
        $categoryId = $this->createCategory('Bút viết', 'but-viet');
        $brandId = $this->createBrand('Thiên Long', 'thien-long');

        for ($number = 1; $number <= 13; $number++) {
            $productId = $this->createProduct(
                sprintf('Bút tìm kiếm %02d', $number),
                sprintf('but-tim-kiem-%02d', $number),
                $categoryId,
                $brandId
            );
            $this->createVariant($productId, sprintf('BUT-%02d', $number), 'Màu xanh', 5000 + $number);
        }

        $response = $this->get(route('search', [
            'q' => 'Bút tìm kiếm',
            'sort' => 'name',
            'page' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('13 sản phẩm');
        $response->assertSee('Bút tìm kiếm 13');
        $response->assertDontSee('Bút tìm kiếm 01');
        $response->assertSee('q=B%C3%BAt%20t%C3%ACm%20ki%E1%BA%BFm', false);
        $response->assertSee('sort=name', false);
        $response->assertSee('data-bs-target="#product-filters"', false);
        $response->assertSee('class="offcanvas-lg offcanvas-start store-filter-offcanvas"', false);
        $response->assertSee('data-product-filter-form', false);
        $response->assertSee('id="filter-search"', false);
        $response->assertSee('name="q"', false);
        $response->assertSee('aria-live="polite"', false);
        $response->assertSee('data-filter-loading-status', false);
    }

    public function test_invalid_filters_and_inactive_catalog_records_are_rejected(): void
    {
        $inactiveCategoryId = $this->createCategory('Danh mục ẩn', 'danh-muc-an', 0);
        $inactiveBrandId = $this->createBrand('Thương hiệu ẩn', 'thuong-hieu-an', 0);

        $category = Category::query()->findOrFail($inactiveCategoryId);

        $this->get(route('products.category', $category))->assertNotFound();

        $this->get(route('products.index', ['sort' => 'price;drop-table']))
            ->assertRedirect()
            ->assertSessionHasErrors('sort');

        $this->get(route('products.index', ['brand' => $inactiveBrandId]))
            ->assertRedirect()
            ->assertSessionHasErrors('brand');
    }

    public function test_product_detail_shows_gallery_variants_inventory_reviews_and_related_products(): void
    {
        $categoryId = $this->createCategory('Bút viết', 'but-viet');
        $brandId = $this->createBrand('Thiên Long', 'thien-long');
        $productId = $this->createProduct(
            'Bút bi xanh',
            'but-bi-xanh',
            $categoryId,
            $brandId,
            description: 'Mực ra đều, phù hợp ghi chép hằng ngày.'
        );
        $variantId = $this->createVariant($productId, 'BUT-BI-05', 'Ngòi 0.5 mm', 10000, 14000);
        $this->createVariant($productId, 'BUT-BI-07', 'Ngòi 0.7 mm', 12000, 15000);

        DB::table('inventory')->insert([
            'variant_id' => $variantId,
            'quantity' => 10,
            'reserved_quantity' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('product_images')->insert([
            [
                'product_id' => $productId,
                'image_url' => 'images/products/but-bi-detail.jpg',
                'is_primary' => 0,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $productId,
                'image_url' => 'images/products/but-bi-main.jpg',
                'is_primary' => 1,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->createApprovedReview($productId, $variantId);

        $relatedProductId = $this->createProduct('Bút mực tím', 'but-muc-tim', $categoryId, $brandId);
        $this->createVariant($relatedProductId, 'BUT-MUC-TIM', 'Ngòi 0.5 mm', 11000);

        $response = $this->get(route('products.show', 'but-bi-xanh'));

        $response->assertOk();
        $response->assertSee('Mực ra đều, phù hợp ghi chép hằng ngày.');
        $response->assertSee('10.000 ₫');
        $response->assertSee('14.000 ₫');
        $response->assertSee('Còn hàng (8)');
        $response->assertSee('data-variant-selector', false);
        $response->assertSee('data-gallery-thumbnail', false);
        $response->assertSee('images/products/but-bi-main.jpg');
        $response->assertSee('Khách hàng thử nghiệm');
        $response->assertSee('Bút mực tím');
        $response->assertSee(route('cart.items.store'), false);
        $response->assertSee('name="variant_id"', false);
        $response->assertSee('name="purchase_intent"', false);
        $response->assertSee('value="checkout"', false);
        $response->assertSee('Mua ngay');
    }

    public function test_product_detail_hides_inactive_products_and_returns_not_found_for_unknown_slugs(): void
    {
        $categoryId = $this->createCategory('Giấy in', 'giay-in');
        $brandId = $this->createBrand('Double A', 'double-a');
        $inactiveProductId = $this->createProduct('Giấy không hoạt động', 'giay-khong-hoat-dong', $categoryId, $brandId, 0);
        $this->createVariant($inactiveProductId, 'GIAY-IN-AN', 'A4', 10000);

        $this->get(route('products.show', 'giay-khong-hoat-dong'))->assertNotFound();
        $this->get(route('products.show', 'khong-ton-tai'))->assertNotFound();
    }

    private function createCategory(string $name, string $slug, int $status = 1): int
    {
        return DB::table('categories')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createBrand(string $name, string $slug, int $status = 1): int
    {
        return DB::table('brands')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createProduct(
        string $name,
        string $slug,
        int $categoryId,
        int $brandId,
        int $status = 1,
        ?string $description = null
    ): int {
        return DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createVariant(
        int $productId,
        string $sku,
        string $name,
        int $price,
        ?int $comparePrice = null
    ): int {
        return DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'sku' => $sku,
            'variant_name' => $name,
            'price' => $price,
            'compare_price' => $comparePrice,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createApprovedReview(int $productId, int $variantId): void
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'Customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userId = DB::table('users')->insertGetId([
            'role_id' => $roleId,
            'full_name' => 'Khách hàng thử nghiệm',
            'email' => 'customer@example.test',
            'password' => 'password',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'order_code' => 'ORDER-TEST-001',
            'subtotal' => 10000,
            'total_amount' => 10000,
            'payment_method' => 'MOCK',
            'receiver_name' => 'Khách hàng thử nghiệm',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Địa chỉ thử nghiệm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $orderItemId = DB::table('order_items')->insertGetId([
            'order_id' => $orderId,
            'variant_id' => $variantId,
            'product_name' => 'Bút bi xanh',
            'variant_name' => 'Ngòi 0.5 mm',
            'sku' => 'BUT-BI-05',
            'price' => 10000,
            'quantity' => 1,
            'subtotal' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('reviews')->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'order_item_id' => $orderItemId,
            'rating' => 5,
            'comment' => 'Sản phẩm tốt.',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
