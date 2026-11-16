<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_customer_can_update_profile_and_user_content_is_escaped(): void
    {
        $user = $this->createCustomer();
        $maliciousName = '<script>alert(1)</script>';

        $this->actingAs($user)
            ->put(route('account.update'), [
                'full_name' => $maliciousName,
                'email' => $user->email,
                'phone' => '0912345678',
                'role_id' => 1,
                'status' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'full_name' => $maliciousName,
            'phone' => '0912345678',
            'status' => 1,
        ]);

        $response = $this->get(route('account.show'));
        $response->assertOk();
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee($maliciousName, false);
    }

    public function test_customer_cannot_change_email_to_another_customers_email(): void
    {
        $user = $this->createCustomer('customer-one@example.test');
        $otherUser = $this->createCustomer('customer-two@example.test');

        $response = $this->actingAs($user)->put(route('account.update'), [
            'full_name' => 'Tên mới',
            'email' => $otherUser->email,
            'phone' => null,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'customer-one@example.test']);
    }

    public function test_customer_can_view_orders_and_saved_addresses_without_exposing_other_accounts(): void
    {
        $user = $this->createCustomer('account-one@example.test');
        $otherUser = $this->createCustomer('account-two@example.test');
        $this->createOrderRecord($user->id, 'ORDER-OWNED-001');
        $this->createOrderRecord($otherUser->id, 'ORDER-PRIVATE-001');

        $response = $this->actingAs($user)->get(route('account.show'));

        $response->assertOk();
        $response->assertSee('ORDER-OWNED-001');
        $response->assertDontSee('ORDER-PRIVATE-001');
    }

    public function test_customer_can_update_password_with_their_current_password(): void
    {
        $user = $this->createCustomer();

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'password-for-test',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check(
            'new-secure-password',
            $user->fresh()->password,
        ));
    }

    public function test_customer_cannot_change_password_without_the_correct_current_password(): void
    {
        $user = $this->createCustomer();

        $this->actingAs($user)
            ->put(route('account.password.update'), [
                'current_password' => 'wrong-current-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password-for-test', $user->fresh()->password));
    }

    public function test_customer_password_change_form_is_present_on_the_account_page(): void
    {
        $user = $this->createCustomer();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Đổi mật khẩu')
            ->assertSee(route('account.password.update'), false)
            ->assertSee('autocomplete="current-password"', false)
            ->assertSee('autocomplete="new-password"', false);
    }

    private function createOrderRecord(int $userId, string $orderCode): int
    {
        return DB::table('orders')->insertGetId([
            'user_id' => $userId,
            'order_code' => $orderCode,
            'subtotal' => 10000,
            'total_amount' => 10000,
            'payment_method' => 'MOCK',
            'receiver_name' => 'Nguyễn An',
            'receiver_phone' => '0900000000',
            'shipping_address' => 'Địa chỉ thử nghiệm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
