<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = $this->createCustomer('order-owner@example.test');
        $otherCustomer = $this->createCustomer('order-visitor@example.test');
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $owner->id,
            'order_code' => 'ORDER-PRIVATE-001',
            'subtotal' => 10000,
            'total_amount' => 10000,
            'payment_method' => 'MOCK',
            'receiver_name' => 'Khách hàng chủ',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Địa chỉ riêng tư',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($otherCustomer)
            ->get(route('orders.show', $orderId))
            ->assertNotFound();
    }

    public function test_customer_can_view_their_order_details(): void
    {
        $user = $this->createCustomer();
        $orderId = DB::table('orders')->insertGetId([
            'user_id' => $user->id,
            'order_code' => 'ORDER-OWNED-001',
            'subtotal' => 10000,
            'total_amount' => 10000,
            'payment_method' => 'MOCK',
            'payment_status' => 'PAID',
            'order_status' => 'PROCESSING',
            'receiver_name' => 'Nguyễn An',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Số 1, Phường A, Quận B, Hà Nội',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('orders.show', $orderId))
            ->assertOk()
            ->assertSee('ORDER-OWNED-001')
            ->assertSee('Thanh toán mô phỏng thành công');
    }
}
