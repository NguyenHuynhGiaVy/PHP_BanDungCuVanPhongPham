<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_admin_routes_require_an_active_admin_and_admin_pages_render(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirectToRoute('login');

        $customer = $this->createCustomer();
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();

        $admin = $this->createAdmin();
        $this->actingAs($admin);

        foreach ([
            'admin.dashboard',
            'admin.products.index',
            'admin.products.create',
            'admin.categories.index',
            'admin.categories.create',
            'admin.brands.index',
            'admin.brands.create',
            'admin.suppliers.index',
            'admin.suppliers.create',
            'admin.inventory.index',
            'admin.orders.index',
            'admin.customers.index',
            'admin.coupons.index',
            'admin.coupons.create',
            'admin.reviews.index',
        ] as $routeName) {
            $this->get(route($routeName))->assertOk();
        }
    }

    public function test_admin_can_create_and_update_categories_brands_and_suppliers(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $this->post(route('admin.categories.store'), [
            'name' => 'Bút viết',
            'slug' => 'but-viet',
            'parent_id' => '',
            'description' => 'Nhóm bút viết.',
            'status' => '1',
        ])->assertRedirectToRoute('admin.categories.index');
        $categoryId = DB::table('categories')->where('slug', 'but-viet')->value('id');
        $this->assertDatabaseHas('categories', ['id' => $categoryId, 'name' => 'Bút viết', 'status' => 1]);

        $childCategoryId = DB::table('categories')->insertGetId([
            'parent_id' => $categoryId,
            'name' => 'Bút gel',
            'slug' => 'but-gel',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->put(route('admin.categories.update', $categoryId), [
            'name' => 'Bút viết',
            'slug' => 'but-viet',
            'parent_id' => $childCategoryId,
            'description' => 'Nhóm bút viết.',
            'status' => '1',
        ])->assertRedirect()->assertSessionHasErrors('parent_id');
        $this->assertDatabaseHas('categories', ['id' => $categoryId, 'parent_id' => null]);

        $this->put(route('admin.categories.update', $categoryId), [
            'name' => 'Bút và mực',
            'slug' => 'but-va-muc',
            'parent_id' => '',
            'description' => 'Đã cập nhật.',
            'status' => '0',
        ])->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseHas('categories', ['id' => $categoryId, 'slug' => 'but-va-muc', 'status' => 0]);

        $this->post(route('admin.brands.store'), [
            'name' => 'Thương hiệu A',
            'slug' => 'thuong-hieu-a',
            'logo' => '',
            'status' => '1',
        ])->assertRedirectToRoute('admin.brands.index');
        $brandId = DB::table('brands')->where('slug', 'thuong-hieu-a')->value('id');
        $this->put(route('admin.brands.update', $brandId), [
            'name' => 'Thương hiệu A cập nhật',
            'slug' => 'thuong-hieu-a',
            'logo' => '',
            'status' => '0',
        ])->assertRedirectToRoute('admin.brands.index');
        $this->assertDatabaseHas('brands', ['id' => $brandId, 'status' => 0]);

        $this->post(route('admin.suppliers.store'), [
            'name' => 'Nhà cung cấp A',
            'email' => 'supplier@example.test',
            'phone' => '0900000001',
            'address' => 'Hà Nội',
            'status' => '1',
        ])->assertRedirectToRoute('admin.suppliers.index');
        $supplierId = DB::table('suppliers')->where('email', 'supplier@example.test')->value('id');
        $this->put(route('admin.suppliers.update', $supplierId), [
            'name' => 'Nhà cung cấp A cập nhật',
            'email' => 'supplier@example.test',
            'phone' => '0900000001',
            'address' => 'Hà Nội',
            'status' => '0',
        ])->assertRedirectToRoute('admin.suppliers.index');
        $this->assertDatabaseHas('suppliers', ['id' => $supplierId, 'status' => 0]);
    }

    public function test_admin_can_create_product_with_multiple_variants_and_empty_inventory(): void
    {
        $admin = $this->createAdmin();
        $records = $this->createProductRecord();

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Thêm biến thể')
            ->assertSee('Tên thuộc tính')
            ->assertDontSee('JSON');

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $records['category_id'],
            'brand_id' => $records['brand_id'],
            'name' => 'Bút gel xanh',
            'slug' => 'but-gel-xanh',
            'description' => 'Mô tả sản phẩm.',
            'status' => '1',
            'variants' => [
                [
                    'sku' => 'GEL-BLUE-001',
                    'variant_name' => 'Ngòi 0.5 mm',
                    'attributes' => [
                        ['name' => 'màu', 'value' => 'xanh'],
                        ['name' => 'ngòi', 'value' => '0.5 mm'],
                    ],
                    'price' => '12000',
                    'compare_price' => '15000',
                ],
                [
                    'sku' => 'GEL-BLUE-002',
                    'variant_name' => 'Ngòi 0.7 mm',
                    'attributes' => [
                        ['name' => 'màu', 'value' => 'xanh'],
                        ['name' => 'ngòi', 'value' => '0.7 mm'],
                    ],
                    'price' => '13000',
                    'compare_price' => '',
                ],
            ],
            'image_url' => 'images/products/gel-blue.jpg',
        ]);

        $productId = DB::table('products')->where('slug', 'but-gel-xanh')->value('id');
        $variantId = DB::table('product_variants')->where('sku', 'GEL-BLUE-001')->value('id');
        $secondVariantId = DB::table('product_variants')->where('sku', 'GEL-BLUE-002')->value('id');
        $response->assertRedirectToRoute('admin.products.index')
            ->assertSessionHas('status', 'Sản phẩm và các biến thể đã được tạo.');
        $this->assertDatabaseHas('products', ['id' => $productId, 'category_id' => $records['category_id'], 'brand_id' => $records['brand_id']]);
        $this->assertDatabaseHas('product_variants', ['id' => $variantId, 'product_id' => $productId, 'price' => '12000.00']);
        $this->assertDatabaseHas('product_variants', ['id' => $secondVariantId, 'product_id' => $productId, 'price' => '13000.00']);
        $this->assertDatabaseHas('inventory', ['variant_id' => $variantId, 'quantity' => 0, 'reserved_quantity' => 0]);
        $this->assertDatabaseHas('inventory', ['variant_id' => $secondVariantId, 'quantity' => 0, 'reserved_quantity' => 0]);
        $this->assertDatabaseHas('product_images', ['product_id' => $productId, 'image_url' => 'images/products/gel-blue.jpg', 'is_primary' => 1]);
        $this->assertSame(
            ['màu' => 'xanh', 'ngòi' => '0.5 mm'],
            json_decode(DB::table('product_variants')->where('id', $variantId)->value('attributes'), true, flags: JSON_THROW_ON_ERROR),
        );

        $this->actingAs($admin)->delete(route('admin.products.destroy', $productId))
            ->assertRedirect()
            ->assertSessionHasErrors('product');
        $this->assertDatabaseHas('products', ['id' => $productId]);
    }

    public function test_product_slug_is_generated_from_its_name_and_can_be_overridden(): void
    {
        $admin = $this->createAdmin();
        $records = $this->createProductRecord();
        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('data-auto-slug-name', false)
            ->assertSee('data-slug-auto-flag', false);

        $productDetails = [
            'category_id' => $records['category_id'],
            'brand_id' => $records['brand_id'],
            'name' => 'Bút bi Thiên Long',
            'slug_is_auto' => '1',
            'status' => '1',
            'variants' => [
                [
                    'sku' => 'THIEN-LONG-001',
                    'variant_name' => 'Mẫu tiêu chuẩn',
                    'price' => '10000',
                    'compare_price' => '',
                ],
            ],
        ];

        $this->post(route('admin.products.store'), $productDetails)
            ->assertRedirectToRoute('admin.products.index');
        $this->assertDatabaseHas('products', ['slug' => 'but-bi-thien-long']);

        $productDetails['variants'][0]['sku'] = 'THIEN-LONG-002';
        $this->post(route('admin.products.store'), $productDetails)
            ->assertRedirectToRoute('admin.products.index');
        $this->assertDatabaseHas('products', ['slug' => 'but-bi-thien-long-2']);

        $productDetails['slug'] = 'but-bi-yeu-thich';
        $productDetails['slug_is_auto'] = '0';
        $productDetails['variants'][0]['sku'] = 'THIEN-LONG-003';
        $this->post(route('admin.products.store'), $productDetails)
            ->assertRedirectToRoute('admin.products.index');
        $this->assertDatabaseHas('products', ['slug' => 'but-bi-yeu-thich']);
    }

    public function test_admin_cannot_create_a_product_with_an_incomplete_variant_attribute(): void
    {
        $admin = $this->createAdmin();
        $records = $this->createProductRecord();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $records['category_id'],
            'brand_id' => $records['brand_id'],
            'name' => 'Bút gel xanh',
            'slug' => 'but-gel-xanh',
            'status' => '1',
            'variants' => [
                [
                    'sku' => 'GEL-BLUE-001',
                    'variant_name' => 'Ngòi 0.5 mm',
                    'attributes' => [['name' => 'màu', 'value' => '']],
                    'price' => '12000',
                ],
            ],
        ]);

        $response->assertSessionHasErrors('variants.0.attributes.0.value');
        $this->assertDatabaseMissing('products', ['slug' => 'but-gel-xanh']);
        $this->assertDatabaseCount('product_variants', 0);
        $this->assertDatabaseCount('inventory', 0);
    }

    public function test_admin_can_update_a_product_variant_with_name_and_value_attributes(): void
    {
        $admin = $this->createAdmin();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);

        $editResponse = $this->actingAs($admin)->get(route('admin.products.edit', $product['product_id']));
        $editResponse->assertOk();
        $document = new \DOMDocument;
        $document->loadHTML($editResponse->getContent());
        $xpath = new \DOMXPath($document);
        $saveButton = $xpath->query('//button[@type="submit" and contains(normalize-space(.), "Lưu thay đổi")]')->item(0);
        $this->assertInstanceOf(\DOMElement::class, $saveButton);
        $this->assertSame('product-form', $saveButton->getAttribute('form'));

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product['product_id']), [
            'category_id' => $product['category_id'],
            'brand_id' => $product['brand_id'],
            'name' => 'Sản phẩm thử nghiệm',
            'slug' => 'san-pham-thu-nghiem',
            'status' => '1',
            'sku' => 'SKU-TEST-001',
            'variant_name' => 'Màu xanh',
            'attributes' => [
                ['name' => 'màu', 'value' => 'xanh'],
                ['name' => 'ngòi', 'value' => '0.5 mm'],
            ],
            'price' => '12500',
            'compare_price' => '',
            'image_url' => '',
        ]);

        $response->assertRedirectToRoute('admin.products.index')
            ->assertSessionHas('status', 'Thông tin sản phẩm đã được cập nhật.');
        $this->assertSame(
            ['màu' => 'xanh', 'ngòi' => '0.5 mm'],
            json_decode(DB::table('product_variants')->where('id', $variantId)->value('attributes'), true, flags: JSON_THROW_ON_ERROR),
        );
    }

    public function test_admin_can_create_a_scoped_product_variant_and_other_products_variants_are_not_visible(): void
    {
        $admin = $this->createAdmin();
        $firstProduct = $this->createProductRecord('first-product');
        $secondProduct = $this->createProductRecord('second-product');
        $otherVariantId = $this->createVariantRecord($secondProduct['product_id'], 'OTHER-PRODUCT-SKU');

        $this->actingAs($admin)
            ->get(route('admin.products.variants.create', $firstProduct['product_id']))
            ->assertOk()
            ->assertSee('Tên thuộc tính')
            ->assertDontSee('JSON');

        $response = $this->actingAs($admin)->post(route('admin.products.variants.store', $firstProduct['product_id']), [
            'sku' => 'FIRST-PRODUCT-SKU',
            'variant_name' => 'Màu đỏ',
            'attributes' => [['name' => 'color', 'value' => 'red']],
            'price' => '25000',
            'compare_price' => '',
            'status' => '1',
        ]);

        $response->assertRedirectToRoute('admin.products.edit', $firstProduct['product_id']);
        $this->assertDatabaseHas('product_variants', ['product_id' => $firstProduct['product_id'], 'sku' => 'FIRST-PRODUCT-SKU']);
        $newVariantId = DB::table('product_variants')->where('sku', 'FIRST-PRODUCT-SKU')->value('id');
        $this->assertDatabaseHas('inventory', ['variant_id' => $newVariantId, 'quantity' => 0]);
        $this->assertSame(
            ['color' => 'red'],
            json_decode(DB::table('product_variants')->where('id', $newVariantId)->value('attributes'), true, flags: JSON_THROW_ON_ERROR),
        );
        $this->put(route('admin.products.variants.update', [$firstProduct['product_id'], $newVariantId]), [
            'sku' => 'FIRST-PRODUCT-SKU',
            'variant_name' => 'Màu xanh',
            'attributes' => [['name' => 'color', 'value' => 'blue']],
            'price' => '26000',
            'compare_price' => '',
            'status' => '1',
        ])->assertRedirectToRoute('admin.products.edit', $firstProduct['product_id']);
        $this->assertSame(
            ['color' => 'blue'],
            json_decode(DB::table('product_variants')->where('id', $newVariantId)->value('attributes'), true, flags: JSON_THROW_ON_ERROR),
        );
        $this->get(route('admin.products.edit', $firstProduct['product_id']))
            ->assertOk()
            ->assertSee('FIRST-PRODUCT-SKU')
            ->assertSee(route('admin.products.variants.edit', [$firstProduct['product_id'], $newVariantId]), false);

        $this->get(route('admin.products.variants.edit', [$firstProduct['product_id'], $otherVariantId]))->assertNotFound();
    }

    public function test_product_image_management_keeps_one_primary_image_and_scopes_images_to_the_product(): void
    {
        $admin = $this->createAdmin();
        $product = $this->createProductRecord();
        $otherProduct = $this->createProductRecord('other-image-owner');
        $firstImageId = DB::table('product_images')->insertGetId([
            'product_id' => $product['product_id'],
            'image_url' => 'images/products/first.jpg',
            'is_primary' => 1,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $secondImageId = DB::table('product_images')->insertGetId([
            'product_id' => $product['product_id'],
            'image_url' => 'images/products/second.jpg',
            'is_primary' => 0,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $otherImageId = DB::table('product_images')->insertGetId([
            'product_id' => $otherProduct['product_id'],
            'image_url' => 'images/products/other.jpg',
            'is_primary' => 1,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.products.images.primary', [$product['product_id'], $secondImageId]));

        $response->assertRedirectToRoute('admin.products.edit', $product['product_id']);
        $this->assertDatabaseHas('product_images', ['id' => $firstImageId, 'is_primary' => 0]);
        $this->assertDatabaseHas('product_images', ['id' => $secondImageId, 'is_primary' => 1]);
        $this->patch(route('admin.products.images.primary', [$product['product_id'], $otherImageId]))->assertNotFound();

        $this->delete(route('admin.products.images.destroy', [$product['product_id'], $secondImageId]))->assertRedirectToRoute('admin.products.edit', $product['product_id']);
        $this->assertDatabaseMissing('product_images', ['id' => $secondImageId]);
        $this->assertDatabaseHas('product_images', ['id' => $firstImageId, 'is_primary' => 1]);
    }

    public function test_inventory_adjustments_update_stock_and_append_a_ledger_entry_atomically(): void
    {
        $admin = $this->createAdmin();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5, 1);
        $supplierId = DB::table('suppliers')->insertGetId([
            'name' => 'Nhà cung cấp thử nghiệm',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.inventory.adjust', DB::table('inventory')->where('variant_id', $variantId)->value('id')), [
            'transaction_type' => 'IMPORT',
            'quantity' => 4,
            'supplier_id' => $supplierId,
            'note' => 'Nhập bổ sung.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inventory', ['variant_id' => $variantId, 'quantity' => 9, 'reserved_quantity' => 1]);
        $this->assertDatabaseHas('inventory_transactions', ['variant_id' => $variantId, 'supplier_id' => $supplierId, 'transaction_type' => 'IMPORT', 'quantity' => 4]);

        $response = $this->post(route('admin.inventory.adjust', DB::table('inventory')->where('variant_id', $variantId)->value('id')), [
            'transaction_type' => 'EXPORT',
            'quantity' => 9,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['quantity' => 'Chỉ còn 8 sản phẩm khả dụng; không thể xuất số lượng yêu cầu.']);
        $this->assertDatabaseHas('inventory', ['variant_id' => $variantId, 'quantity' => 9, 'reserved_quantity' => 1]);
        $this->assertDatabaseCount('inventory_transactions', 1);

        $this->post(route('admin.inventory.adjust', DB::table('inventory')->where('variant_id', $variantId)->value('id')), [
            'transaction_type' => 'EXPORT',
            'quantity' => 3,
        ])->assertRedirect();
        $this->assertDatabaseHas('inventory', ['variant_id' => $variantId, 'quantity' => 6, 'reserved_quantity' => 1]);
        $this->assertDatabaseHas('inventory_transactions', ['variant_id' => $variantId, 'transaction_type' => 'EXPORT', 'quantity' => 3]);
        $this->assertDatabaseCount('inventory_transactions', 2);
    }

    public function test_admin_dashboard_shows_revenue_from_paid_non_cancelled_orders(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $paidOrderId = $this->createOrder($customer->id, 'PROCESSING', 'PAID', 'PAID-ORDER');
        $unpaidOrderId = $this->createOrder($customer->id, 'PROCESSING', 'PENDING', 'UNPAID-ORDER');
        $cancelledOrderId = $this->createOrder($customer->id, 'CANCELLED', 'PAID', 'CANCELLED-ORDER');
        DB::table('orders')->where('id', $paidOrderId)->update(['total_amount' => '15000.00']);
        DB::table('orders')->where('id', $unpaidOrderId)->update(['total_amount' => '9000.00']);
        DB::table('orders')->where('id', $cancelledOrderId)->update(['total_amount' => '7000.00']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Doanh thu đã thanh toán')
            ->assertSee('15.000 ₫')
            ->assertViewHas('statistics', fn (array $statistics): bool => (float) $statistics['revenue'] === 15000.0);
    }

    public function test_order_status_can_only_advance_without_changing_payment_state(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $orderId = $this->createOrder($customer->id, 'PROCESSING', 'PAID');
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertSee(route('admin.orders.show', $orderId), false);

        $response = $this->patch(route('admin.orders.status.update', $orderId), [
            'order_status' => 'SHIPPED',
        ]);

        $response->assertRedirectToRoute('admin.orders.show', $orderId);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'order_status' => 'SHIPPED', 'payment_status' => 'PAID']);
        $this->get(route('admin.orders.show', $orderId))
            ->assertOk()
            ->assertSee('Chi tiết đơn hàng')
            ->assertSee('TEST-ORDER-001');

        $response = $this->patch(route('admin.orders.status.update', $orderId), [
            'order_status' => 'PROCESSING',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['order_status' => 'Không thể chuyển đơn hàng sang trạng thái đã chọn.']);
        $this->assertDatabaseHas('orders', ['id' => $orderId, 'order_status' => 'SHIPPED', 'payment_status' => 'PAID']);

        $unpaidOrderId = $this->createOrder(
            $this->createCustomer('unpaid-customer@example.test')->id,
            'PROCESSING',
            'PENDING',
            'TEST-ORDER-002',
        );
        $this->patch(route('admin.orders.status.update', $unpaidOrderId), ['order_status' => 'SHIPPED'])
            ->assertRedirect()
            ->assertSessionHasErrors('order_status');
        $this->assertDatabaseHas('orders', ['id' => $unpaidOrderId, 'order_status' => 'PROCESSING', 'payment_status' => 'PENDING']);
    }

    public function test_customer_status_can_be_changed_without_changing_their_role(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $response = $this->actingAs($admin)->patch(route('admin.customers.update', $customer), [
            'status' => '0',
            'role_id' => DB::table('roles')->where('name', 'ADMIN')->value('id'),
        ]);

        $response->assertRedirectToRoute('admin.customers.show', $customer);
        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status' => 0, 'role_id' => $customer->role_id]);
        $this->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee($customer->email);
        $this->get(route('admin.customers.show', $admin))->assertNotFound();
    }

    public function test_coupon_codes_are_normalized_and_usage_history_is_not_overwritten(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.coupons.store'), $this->couponDetails([
            'code' => ' spring-10 ',
        ]));

        $response->assertRedirectToRoute('admin.coupons.index');
        $couponId = DB::table('coupons')->where('code', 'SPRING-10')->value('id');
        $this->assertDatabaseHas('coupons', ['id' => $couponId, 'code' => 'SPRING-10', 'used_count' => 0]);

        DB::table('coupons')->where('id', $couponId)->update(['used_count' => 2]);
        $response = $this->put(route('admin.coupons.update', $couponId), $this->couponDetails([
            'code' => 'SPRING-10',
            'usage_limit' => 3,
        ]));

        $response->assertRedirectToRoute('admin.coupons.index');
        $this->assertDatabaseHas('coupons', ['id' => $couponId, 'used_count' => 2, 'usage_limit' => 3]);
        $this->get(route('admin.coupons.edit', $couponId))->assertOk()->assertSee('SPRING-10');
    }

    public function test_coupon_percentage_must_not_exceed_one_hundred(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.coupons.store'), $this->couponDetails([
            'discount_value' => '101',
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('discount_value');
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_review_status_changes_are_reflected_in_storage(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $orderId = $this->createOrder($customer->id);
        $orderItemId = DB::table('order_items')->insertGetId([
            'order_id' => $orderId,
            'variant_id' => $variantId,
            'product_name' => 'Sản phẩm thử nghiệm',
            'variant_name' => 'Mẫu tiêu chuẩn',
            'sku' => 'SKU-TEST-001',
            'price' => '12500.00',
            'quantity' => 1,
            'subtotal' => '12500.00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $reviewId = DB::table('reviews')->insertGetId([
            'user_id' => $customer->id,
            'product_id' => $product['product_id'],
            'order_item_id' => $orderItemId,
            'rating' => 5,
            'comment' => 'Đóng gói cẩn thận.',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.reviews.update', $reviewId), ['status' => 1]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', ['id' => $reviewId, 'status' => 1, 'comment' => 'Đóng gói cẩn thận.']);
        $this->get(route('admin.reviews.index'))->assertOk()->assertSee('Đóng gói cẩn thận.');
    }

    public function test_supplier_with_inventory_history_cannot_be_deleted(): void
    {
        $admin = $this->createAdmin();
        $supplierId = DB::table('suppliers')->insertGetId([
            'name' => 'Nhà cung cấp có lịch sử',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        DB::table('inventory_transactions')->insert([
            'variant_id' => $variantId,
            'supplier_id' => $supplierId,
            'transaction_type' => 'IMPORT',
            'quantity' => 2,
            'note' => 'Lịch sử nhập kho.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.suppliers.destroy', $supplierId));

        $response->assertRedirect();
        $response->assertSessionHasErrors('supplier');
        $this->assertDatabaseHas('suppliers', ['id' => $supplierId]);
        $this->assertDatabaseHas('inventory_transactions', ['supplier_id' => $supplierId]);
    }

    private function createAdmin(): User
    {
        $roleId = DB::table('roles')->where('name', 'ADMIN')->value('id');

        if ($roleId === null) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'ADMIN',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return User::query()->create([
            'role_id' => $roleId,
            'full_name' => 'Quản trị thử nghiệm',
            'email' => 'admin@example.test',
            'password' => Hash::make('admin-password-test'),
            'status' => 1,
        ]);
    }

    private function createOrder(
        int $userId,
        string $orderStatus = 'PROCESSING',
        string $paymentStatus = 'PAID',
        string $orderCode = 'TEST-ORDER-001',
    ): int {
        return DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'order_code' => $orderCode,
            'subtotal' => '12500.00',
            'discount_amount' => '0.00',
            'shipping_fee' => '0.00',
            'total_amount' => '12500.00',
            'payment_method' => 'MOCK',
            'payment_status' => $paymentStatus,
            'order_status' => $orderStatus,
            'receiver_name' => 'Nguyễn An',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Số 1, Hà Nội',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function couponDetails(array $overrides = []): array
    {
        return [
            'code' => 'SPRING-10',
            'discount_type' => 'percentage',
            'discount_value' => '10',
            'min_order_value' => '100000',
            'max_discount' => '50000',
            'usage_limit' => '10',
            'start_date' => '2025-01-01T00:00',
            'end_date' => '2026-12-31T23:59',
            'status' => '1',
            ...$overrides,
        ];
    }
}
