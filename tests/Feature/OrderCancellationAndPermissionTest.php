<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Permission;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use App\Services\VNPayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class OrderCancellationAndPermissionTest extends TestCase
{
    use CreatesCustomerCommerceRecords, RefreshDatabase;

    private function createStaffUser(array $permissionCodes = []): User
    {
        $role = Role::firstOrCreate(['name' => 'STAFF'], ['description' => 'Nhân viên']);
        $permissionIds = [];

        foreach ($permissionCodes as $code) {
            $perm = Permission::firstOrCreate(
                ['code' => $code],
                ['name' => $code, 'group' => 'Test', 'sort_order' => 1]
            );
            $permissionIds[] = $perm->id;
        }

        $role->permissions()->sync($permissionIds);

        return User::factory()->create([
            'role_id' => $role->id,
            'status' => 1,
        ]);
    }

    private function createAdminUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'ADMIN'], ['description' => 'Quản trị viên']);

        return User::factory()->create([
            'role_id' => $role->id,
            'status' => 1,
        ]);
    }

    private function createSampleOrder(User $customer, string $status = 'PENDING', int $quantity = 2): array
    {
        $records = $this->createProductRecord('sp-test-'.uniqid());
        $variantId = $this->createVariantRecord($records['product_id'], 'SKU-'.uniqid(), 50000);
        $this->createInventoryRecord($variantId, 10, 0);

        $variant = ProductVariant::find($variantId);
        $inventory = Inventory::where('variant_id', $variantId)->first();

        $order = Order::create([
            'user_id' => $customer->id,
            'order_code' => 'TEST-ORD-'.uniqid(),
            'subtotal' => 50000 * $quantity,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'total_amount' => 50000 * $quantity,
            'payment_method' => 'COD',
            'payment_status' => 'PENDING',
            'order_status' => $status,
            'receiver_name' => $customer->full_name,
            'receiver_phone' => '0912345678',
            'shipping_address' => '123 Đường Test, TP.HCM',
        ]);

        $order->items()->create([
            'variant_id' => $variant->id,
            'product_name' => 'Sản phẩm thử nghiệm',
            'variant_name' => $variant->variant_name,
            'sku' => $variant->sku,
            'price' => 50000,
            'quantity' => $quantity,
            'subtotal' => 50000 * $quantity,
        ]);

        $order->payments()->create([
            'payment_method' => 'COD',
            'amount' => 50000 * $quantity,
            'status' => 'PENDING',
        ]);

        return [$order, $variant, $inventory];
    }

    public function test_customer_can_cancel_pending_order_and_inventory_is_refunded(): void
    {
        $customer = $this->createCustomer('user1@test.com');
        [$order, $variant, $inventory] = $this->createSampleOrder($customer, 'PENDING', 3);

        $initialQty = $inventory->quantity; // 10

        $response = $this->actingAs($customer)
            ->patch(route('orders.cancel', $order));

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertEquals('CANCELLED', $order->fresh()->order_status);
        $this->assertEquals($initialQty + 3, $inventory->fresh()->quantity);

        $this->assertDatabaseHas('inventory_transactions', [
            'order_id' => $order->id,
            'variant_id' => $variant->id,
            'transaction_type' => 'IMPORT',
            'quantity' => 3,
        ]);
    }

    public function test_customer_cannot_cancel_shipped_order(): void
    {
        $customer = $this->createCustomer('user2@test.com');
        [$order] = $this->createSampleOrder($customer, 'SHIPPED', 2);

        $response = $this->actingAs($customer)
            ->patch(route('orders.cancel', $order));

        $response->assertSessionHasErrors(['order']);
        $this->assertEquals('SHIPPED', $order->fresh()->order_status);
    }

    public function test_vnpay_failure_cancels_order_and_refunds_inventory(): void
    {
        $customer = $this->createCustomer('user_vnpay@test.com');
        [$order, $variant, $inventory] = $this->createSampleOrder($customer, 'PENDING', 2);
        $order->update(['payment_method' => 'VNPAY']);

        $initialQty = $inventory->quantity;

        // Mock VNPayService to return false for valid signature or non-00 code
        $vnPayService = Mockery::mock(VNPayService::class);
        $vnPayService->shouldReceive('validateSignature')->andReturn(true);
        $this->app->instance(VNPayService::class, $vnPayService);

        $response = $this->actingAs($customer)
            ->get(route('checkout.vnpay.return', [
                'vnp_TxnRef' => $order->id.'_123456',
                'vnp_ResponseCode' => '24', // Customer cancelled
            ]));

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHasErrors(['payment']);

        $this->assertEquals('CANCELLED', $order->fresh()->order_status);
        $this->assertEquals('FAILED', $order->fresh()->payment_status);
        $this->assertEquals($initialQty + 2, $inventory->fresh()->quantity);
    }

    public function test_admin_can_update_order_to_cancelled_and_inventory_is_refunded(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomer('user3@test.com');
        [$order, $variant, $inventory] = $this->createSampleOrder($customer, 'PENDING', 2);

        $initialQty = $inventory->quantity;

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.status.update', $order), [
                'order_status' => 'CANCELLED',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertEquals('CANCELLED', $order->fresh()->order_status);
        $this->assertEquals($initialQty + 2, $inventory->fresh()->quantity);
    }

    public function test_admin_can_update_shipped_order_to_delivered_and_marks_cod_paid(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomer('user_shipped@test.com');
        [$order] = $this->createSampleOrder($customer, 'SHIPPED', 2);

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.status.update', $order), [
                'order_status' => 'DELIVERED',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $freshOrder = $order->fresh();
        $this->assertEquals('DELIVERED', $freshOrder->order_status);
        $this->assertEquals('PAID', $freshOrder->payment_status);
        $this->assertEquals('SUCCESS', $freshOrder->payments()->first()->status);
        $this->assertNotNull($freshOrder->payments()->first()->paid_at);
    }

    public function test_admin_can_cancel_shipped_order_and_inventory_is_refunded(): void
    {
        $admin = $this->createAdminUser();
        $customer = $this->createCustomer('user_shipped_cancel@test.com');
        [$order, $variant, $inventory] = $this->createSampleOrder($customer, 'SHIPPED', 2);

        $initialQty = $inventory->quantity;

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.status.update', $order), [
                'order_status' => 'CANCELLED',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertEquals('CANCELLED', $order->fresh()->order_status);
        $this->assertEquals($initialQty + 2, $inventory->fresh()->quantity);
    }

    public function test_staff_without_manage_staff_cannot_access_staff_management(): void
    {
        $staff = $this->createStaffUser(['view_dashboard']);

        $response = $this->actingAs($staff)
            ->get(route('admin.staff.index'));

        $response->assertForbidden();
    }

    public function test_staff_with_manage_staff_can_access_staff_management(): void
    {
        $staff = $this->createStaffUser(['manage_staff']);

        $response = $this->actingAs($staff)
            ->get(route('admin.staff.index'));

        $response->assertOk();
    }

    public function test_staff_without_manage_permissions_cannot_access_permissions(): void
    {
        $staff = $this->createStaffUser(['view_dashboard']);

        $response = $this->actingAs($staff)
            ->get(route('admin.permissions.index'));

        $response->assertForbidden();
    }

    public function test_contact_form_saves_to_database_and_returns_success(): void
    {
        $data = [
            'name' => 'Nguyễn Văn Test',
            'phone' => '0987654321',
            'email' => 'test@example.com',
            'subject' => 'Hỏi báo giá sỉ',
            'message' => 'Tôi muốn mua 100 cây bút bi Thiên Long.',
        ];

        $response = $this->post(route('contact.send'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('contacts', [
            'email' => 'test@example.com',
            'subject' => 'Hỏi báo giá sỉ',
            'name' => 'Nguyễn Văn Test',
        ]);
    }

    public function test_application_timezone_is_vietnam_bangkok_jakarta(): void
    {
        $this->assertEquals('Asia/Ho_Chi_Minh', config('app.timezone'));
    }
}
