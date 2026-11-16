<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_guest_can_view_login_and_registration_forms(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Đăng nhập');
        $this->get(route('register'))->assertOk()->assertSee('Tạo tài khoản');
    }

    public function test_registration_creates_and_authenticates_a_customer_without_honoring_submitted_role(): void
    {
        $customerRoleId = DB::table('roles')->insertGetId([
            'name' => 'CUSTOMER',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $administratorRoleId = DB::table('roles')->insertGetId([
            'name' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post(route('register.store'), [
            'full_name' => 'Nguyễn An',
            'email' => 'nguyen.an@example.test',
            'phone' => '0900000000',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
            'role_id' => $administratorRoleId,
        ]);

        $user = User::query()->where('email', 'nguyen.an@example.test')->firstOrFail();
        $response->assertRedirectToRoute('account.show');
        $this->assertAuthenticatedAs($user);
        $this->assertSame($customerRoleId, $user->role_id);
        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_inactive_accounts_cannot_log_in(): void
    {
        $user = $this->createInactiveCustomer();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password-for-test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_rejects_an_array_email_with_validation_errors(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => ['invalid'],
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_customer_can_log_in_and_log_out(): void
    {
        $user = $this->createCustomer();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password-for-test',
        ])->assertRedirectToRoute('account.show');

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirectToRoute('home');

        $this->assertGuest();
    }

    public function test_admin_can_log_in_with_normalized_email_and_is_redirected_to_dashboard(): void
    {
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin = User::query()->create([
            'role_id' => $adminRoleId,
            'full_name' => 'Quản trị thử nghiệm',
            'email' => 'admin@example.test',
            'password' => 'secure-admin-password',
            'status' => 1,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => '  ADMIN@EXAMPLE.TEST  ',
            'password' => 'secure-admin-password',
        ]);

        $response->assertRedirectToRoute('admin.dashboard');
        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_registration_reports_missing_customer_role_without_creating_a_user(): void
    {
        $response = $this->post(route('register.store'), [
            'full_name' => 'Nguyễn An',
            'email' => 'nguyen.an@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'nguyen.an@example.test']);
    }

    private function createInactiveCustomer(): User
    {
        $roleId = DB::table('roles')->insertGetId([
            'name' => 'CUSTOMER',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::query()->create([
            'role_id' => $roleId,
            'full_name' => 'Tài khoản khóa',
            'email' => 'inactive@example.test',
            'password' => 'password-for-test',
            'status' => 0,
        ]);
    }
}
