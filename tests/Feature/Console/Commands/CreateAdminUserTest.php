<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_creates_one_active_admin_with_normalized_email_and_hidden_password(): void
    {
        Role::query()->create(['name' => 'ADMIN']);

        $this->artisan('admin:create-user')
            ->expectsQuestion('Tên đầy đủ của Admin', 'Quản trị cửa hàng')
            ->expectsQuestion('Email Admin', '  ADMIN@EXAMPLE.TEST ')
            ->expectsQuestion('Mật khẩu mới (nhập ẩn)', 'admin-password-secure')
            ->expectsQuestion('Nhập lại mật khẩu (nhập ẩn)', 'admin-password-secure')
            ->expectsOutputToContain('Đã tạo tài khoản Admin')
            ->doesntExpectOutputToContain('admin-password-secure')
            ->assertSuccessful();

        $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();
        $this->assertSame(1, $admin->status);
        $this->assertSame('ADMIN', $admin->role->name);
        $this->assertTrue(Hash::check('admin-password-secure', $admin->password));
        $this->assertDatabaseCount('users', 1);
    }

    public function test_command_refuses_an_existing_email_without_creating_an_admin(): void
    {
        Role::query()->create(['name' => 'ADMIN']);
        Role::query()->create(['name' => 'CUSTOMER']);
        $customerRoleId = DB::table('roles')->where('name', 'CUSTOMER')->value('id');
        User::query()->create([
            'role_id' => $customerRoleId,
            'full_name' => 'Khách hàng hiện có',
            'email' => 'existing@example.test',
            'password' => 'existing-customer-password',
            'status' => 1,
        ]);

        $this->artisan('admin:create-user')
            ->expectsQuestion('Tên đầy đủ của Admin', 'Quản trị cửa hàng')
            ->expectsQuestion('Email Admin', 'EXISTING@example.test')
            ->expectsOutputToContain('email')
            ->assertFailed();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => 'existing@example.test', 'role_id' => $customerRoleId]);
    }

    public function test_command_rejects_mismatched_password_confirmation_without_writing_a_user(): void
    {
        Role::query()->create(['name' => 'ADMIN']);

        $this->artisan('admin:create-user')
            ->expectsQuestion('Tên đầy đủ của Admin', 'Quản trị cửa hàng')
            ->expectsQuestion('Email Admin', 'admin@example.test')
            ->expectsQuestion('Mật khẩu mới (nhập ẩn)', 'admin-password-secure')
            ->expectsQuestion('Nhập lại mật khẩu (nhập ẩn)', 'different-secure-password')
            ->expectsOutputToContain('Hai lần nhập mật khẩu không khớp.')
            ->doesntExpectOutputToContain('admin-password-secure')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_command_fails_without_an_existing_admin_role(): void
    {
        $this->artisan('admin:create-user')
            ->expectsOutputToContain('Không tìm thấy role ADMIN')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }
}
