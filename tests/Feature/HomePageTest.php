<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_homepage_displays_active_catalog_products_and_brands(): void
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Bút bi',
            'slug' => 'but-bi',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $brandId = DB::table('brands')->insertGetId([
            'name' => 'Thiên Long',
            'slug' => 'thien-long',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $productId = DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => 'Bút bi xanh',
            'slug' => 'but-bi-xanh',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $variantId = DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'sku' => 'TEST-BUT-001',
            'variant_name' => 'Ngòi 0.5 mm',
            'price' => 10000,
            'compare_price' => 14000,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('inventory')->insert([
            'variant_id' => $variantId,
            'quantity' => 10,
            'reserved_quantity' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $reviewer = $this->createCustomer('homepage-reviewer@example.test');
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $reviewer->id,
            'order_code' => 'ORDER-HOME-REVIEW',
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
            'sku' => 'TEST-BUT-001',
            'price' => 10000,
            'quantity' => 1,
            'subtotal' => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('reviews')->insert([
            'user_id' => $reviewer->id,
            'product_id' => $productId,
            'order_item_id' => $orderItemId,
            'rating' => 5,
            'comment' => 'Sản phẩm tốt.',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->insert([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => 'Bút không hoạt động',
            'slug' => 'but-khong-hoat-dong',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Bút bi');
        $response->assertSee('Bút bi xanh');
        $response->assertSee('Thiên Long');
        $response->assertSee('10.000 ₫');
        $response->assertSee('14.000 ₫');
        $response->assertSee('Còn hàng (8)');
        $response->assertSee('-29%');
        $response->assertSee('aria-label="Đánh giá trung bình 5,0 trên 5 từ 1 đánh giá"', false);
        $response->assertSee('bi bi-pen', false);
        $response->assertSee(route('products.category', 'but-bi'), false);
        $response->assertSee(route('login'), false);
        $response->assertViewHas('promotionalProducts', fn ($products): bool => $products->pluck('slug')->all() === ['but-bi-xanh']);
        $response->assertDontSee('Bút không hoạt động');
    }

    public function test_homepage_shows_empty_states_when_catalog_is_empty(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Danh mục sản phẩm đang được cập nhật.');
        $response->assertSee('Sản phẩm sẽ hiển thị tại đây sau khi được cập nhật vào cửa hàng.');
        $response->assertSee('Chưa có dữ liệu đơn hàng để xếp hạng');
        $response->assertSee('Hiện chưa có giá khuyến mãi');
        $response->assertSee('Thương hiệu đang được cập nhật.');
    }

    public function test_homepage_ranks_best_sellers_using_paid_processing_order_items(): void
    {
        $user = $this->createCustomer();
        $bestSelling = $this->createProductRecord('ban-chay-nhieu', ['name' => 'Bút bán chạy nhiều']);
        $secondBestSelling = $this->createProductRecord('ban-chay-it', ['name' => 'Bút bán chạy ít']);
        $cancelledProduct = $this->createProductRecord('don-da-huy', ['name' => 'Bút đơn đã hủy']);
        $unpaidProduct = $this->createProductRecord('don-chua-thanh-toan', ['name' => 'Bút chưa thanh toán']);

        $bestSellingVariant = $this->createVariantRecord($bestSelling['product_id'], 'BEST-SELLER', 10000);
        $secondBestSellingVariant = $this->createVariantRecord($secondBestSelling['product_id'], 'SECOND-SELLER', 10000);
        $cancelledVariant = $this->createVariantRecord($cancelledProduct['product_id'], 'CANCELLED-SELLER', 10000);
        $unpaidVariant = $this->createVariantRecord($unpaidProduct['product_id'], 'UNPAID-SELLER', 10000);

        $this->createInventoryRecord($bestSellingVariant, 10);
        $this->createInventoryRecord($secondBestSellingVariant, 10);
        $this->createInventoryRecord($cancelledVariant, 10);
        $this->createInventoryRecord($unpaidVariant, 10);
        $this->createOrderItem($user->id, $bestSellingVariant, 'ORDER-BEST-SELLER', 5);
        $this->createOrderItem($user->id, $secondBestSellingVariant, 'ORDER-SECOND-SELLER', 2);
        $this->createOrderItem($user->id, $cancelledVariant, 'ORDER-CANCELLED', 50, 'PAID', 'CANCELLED');
        $this->createOrderItem($user->id, $unpaidVariant, 'ORDER-UNPAID', 100, 'PENDING', 'PROCESSING');

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder(['Bút bán chạy nhiều', 'Bút bán chạy ít']);
        $response->assertViewHas('bestSellingProducts', fn ($products): bool => $products->pluck('slug')->all() === [
            'ban-chay-nhieu',
            'ban-chay-it',
        ]);
    }

    public function test_authenticated_customer_sees_add_to_cart_and_wishlist_actions_on_product_cards(): void
    {
        $user = $this->createCustomer();
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Bút bi',
            'slug' => 'but-bi',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $brandId = DB::table('brands')->insertGetId([
            'name' => 'Thiên Long',
            'slug' => 'thien-long',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => 'Bút bi xanh',
            'slug' => 'but-bi-xanh',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $variantId = $this->createVariantRecord($productId);
        $this->createInventoryRecord($variantId, 5);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertSee('action="'.route('cart.items.store').'"', false);
        $response->assertSee('action="'.route('wishlist.store', $productId).'"', false);
        $response->assertSee('name="variant_id" value="'.$variantId.'"', false);
    }

    public function test_product_card_disables_cart_action_when_inventory_is_empty(): void
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Bút bi',
            'slug' => 'but-bi',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $brandId = DB::table('brands')->insertGetId([
            'name' => 'Thiên Long',
            'slug' => 'thien-long',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => 'Bút đã hết hàng',
            'slug' => 'but-da-het-hang',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $variantId = $this->createVariantRecord($productId);
        $this->createInventoryRecord($variantId, 0);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Bút đã hết hàng');
        $response->assertSee('Tạm hết hàng');
        $response->assertSee('class="btn store-product-add" type="button" disabled', false);
        $response->assertSee('Chưa có đánh giá');
    }

    public function test_homepage_promotional_banners_link_to_the_product_listing(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Không gian làm việc tiện nghi – Học tập hiệu quả');
        $response->assertSee('Mua sắm ngay');
        $response->assertSee('Sắp xếp góc riêng, khơi nguồn ý tưởng mới.');
        $response->assertSee('href="'.route('products.index').'"', false);
    }

    private function createOrderItem(
        int $userId,
        int $variantId,
        string $orderCode,
        int $quantity,
        string $paymentStatus = 'PAID',
        string $orderStatus = 'PROCESSING'
    ): void {
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'order_code' => $orderCode,
            'subtotal' => 10000 * $quantity,
            'total_amount' => 10000 * $quantity,
            'payment_method' => 'MOCK',
            'payment_status' => $paymentStatus,
            'order_status' => $orderStatus,
            'receiver_name' => 'Khách hàng thử nghiệm',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Địa chỉ thử nghiệm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('order_items')->insert([
            'order_id' => $orderId,
            'variant_id' => $variantId,
            'product_name' => $orderCode,
            'variant_name' => 'Mẫu tiêu chuẩn',
            'sku' => $orderCode,
            'price' => 10000,
            'quantity' => $quantity,
            'subtotal' => 10000 * $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
