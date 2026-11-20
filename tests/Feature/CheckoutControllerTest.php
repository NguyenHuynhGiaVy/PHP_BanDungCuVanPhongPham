<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_vnpay_payment_url_contains_required_parameters(): void
    {
        config()->set('services.vnpay.tmn_code', 'TESTTMNCODE');
        config()->set('services.vnpay.hash_secret', 'TESTHASHSECRET');
        config()->set('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

        $user = $this->createCustomer();
        $product = $this->createProductRecord('vnpay-thanh-toan');
        $variantId = $this->createVariantRecord($product['product_id'], 'VNPAY-001', 12500);
        $this->createInventoryRecord($variantId, 5);

        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $user->id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cart_items')->insert([
            'cart_id' => $cartId,
            'variant_id' => $variantId,
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), [
                ...$this->checkoutDetails(),
                'payment_method' => 'VNPAY',
            ]);

        $response->assertRedirect();
        $location = (string) $response->headers->get('Location');
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        $this->assertSame('TESTTMNCODE', $query['vnp_TmnCode']);
        $this->assertSame('2.1.0', $query['vnp_Version']);
        $this->assertSame('pay', $query['vnp_Command']);
        $this->assertSame('1250000', $query['vnp_Amount']);
        $this->assertSame('HMACSHA512', $query['vnp_SecureHashType']);
        $this->assertArrayHasKey('vnp_SecureHash', $query);
    }

    public function test_mock_checkout_creates_order_payment_address_and_inventory_transaction_atomically(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord('but-bi-xanh');
        $variantId = $this->createVariantRecord($product['product_id'], 'BUT-BI-001', 12500);
        $this->createInventoryRecord($variantId, 8, 1);
        DB::table('product_images')->insert([
            'product_id' => $product['product_id'],
            'image_url' => 'images/products/checkout-item.jpg',
            'is_primary' => 1,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $user->id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cart_items')->insert([
            'cart_id' => $cartId,
            'variant_id' => $variantId,
            'quantity' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Thanh toán mô phỏng')
            ->assertSee('images/products/checkout-item.jpg')
            ->assertSee('id="coupon_code"', false)
            ->assertSee('Mã khuyến mãi');

        $response = $this->actingAs($user)->post(route('checkout.store'), $this->checkoutDetails());

        $orderId = DB::table('orders')->where('user_id', $user->id)->value('id');
        $response->assertRedirectToRoute('orders.show', $orderId);
        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'user_id' => $user->id,
            'subtotal' => '25000.00',
            'total_amount' => '25000.00',
            'payment_method' => 'MOCK',
            'payment_status' => 'PAID',
            'order_status' => 'PROCESSING',
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_name' => 'Sản phẩm thử nghiệm',
            'quantity' => 2,
            'subtotal' => '25000.00',
        ]);
        $this->assertDatabaseHas('payments', [
            'order_id' => $orderId,
            'payment_method' => 'MOCK',
            'amount' => '25000.00',
            'status' => 'SUCCESS',
        ]);
        $this->assertDatabaseHas('inventory', [
            'variant_id' => $variantId,
            'quantity' => 6,
            'reserved_quantity' => 1,
        ]);
        $this->assertDatabaseHas('inventory_transactions', [
            'variant_id' => $variantId,
            'order_id' => $orderId,
            'transaction_type' => 'EXPORT',
            'quantity' => 2,
        ]);
        $this->assertDatabaseCount('cart_items', 0);
        $this->get(route('orders.show', $orderId))
            ->assertOk()
            ->assertSee('Đặt hàng thành công')
            ->assertSee('Tiếp tục mua sắm');
    }

    public function test_checkout_rolls_back_order_when_cart_quantity_exceeds_available_stock(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 2, 1);
        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $user->id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cart_items')->insert([
            'cart_id' => $cartId,
            'variant_id' => $variantId,
            'quantity' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), $this->checkoutDetails());

        $response->assertRedirect();
        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('addresses', 0);
        $this->assertDatabaseHas('cart_items', ['cart_id' => $cartId, 'quantity' => 2]);
        $this->assertDatabaseHas('inventory', ['variant_id' => $variantId, 'quantity' => 2]);
    }

    public function test_checkout_requires_explicit_mock_payment_confirmation(): void
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            ...$this->checkoutDetails(),
            'mock_payment_confirmed' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('mock_payment_confirmed');
        $this->assertDatabaseCount('orders', 0);
    }

    private function checkoutDetails(): array
    {
        return [
            'receiver_name' => 'Nguyễn An',
            'phone' => '0900000000',
            'province' => 'Hà Nội',
            'district' => 'Ba Đình',
            'ward' => 'Phúc Xá',
            'detail_address' => 'Số 1 phố Hàng',
            'note' => 'Giao giờ hành chính',
            'mock_payment_confirmed' => '1',
            'payment_method' => 'MOCK',
        ];
    }
}
