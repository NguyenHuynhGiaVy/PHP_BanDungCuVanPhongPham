<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class AddressBookControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_customer_can_add_an_address_and_the_first_address_becomes_default(): void
    {
        $user = $this->createCustomer();

        $this->actingAs($user)
            ->post(route('account.addresses.store'), $this->addressDetails())
            ->assertRedirect();

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'receiver_name' => 'Nguyễn An',
            'detail_address' => 'Số 1 phố Hàng',
            'is_default' => 1,
        ]);
    }

    public function test_customer_can_edit_an_address_and_make_only_that_address_default(): void
    {
        $user = $this->createCustomer();
        $firstAddressId = $this->createAddress($user->id, 'Địa chỉ cũ', true);
        $secondAddressId = $this->createAddress($user->id, 'Địa chỉ phụ');

        $this->actingAs($user)
            ->put(route('account.addresses.update', $secondAddressId), [
                ...$this->addressDetails(),
                'receiver_name' => 'Người nhận mới',
                'address_id' => $secondAddressId,
                'is_default' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('addresses', [
            'id' => $firstAddressId,
            'is_default' => 0,
        ]);
        $this->assertDatabaseHas('addresses', [
            'id' => $secondAddressId,
            'receiver_name' => 'Người nhận mới',
            'is_default' => 1,
        ]);
    }

    public function test_customer_can_set_an_existing_address_as_default(): void
    {
        $user = $this->createCustomer();
        $firstAddressId = $this->createAddress($user->id, 'Địa chỉ cũ', true);
        $secondAddressId = $this->createAddress($user->id, 'Địa chỉ mới');

        $this->actingAs($user)
            ->patch(route('account.addresses.default', $secondAddressId))
            ->assertRedirect();

        $this->assertDatabaseHas('addresses', ['id' => $firstAddressId, 'is_default' => 0]);
        $this->assertDatabaseHas('addresses', ['id' => $secondAddressId, 'is_default' => 1]);
    }

    public function test_deleting_a_default_address_promotes_the_most_recent_saved_address(): void
    {
        $user = $this->createCustomer();
        $defaultAddressId = $this->createAddress($user->id, 'Địa chỉ mặc định', true);
        $remainingAddressId = $this->createAddress($user->id, 'Địa chỉ còn lại');

        $this->actingAs($user)
            ->delete(route('account.addresses.destroy', $defaultAddressId))
            ->assertRedirect();

        $this->assertDatabaseMissing('addresses', ['id' => $defaultAddressId]);
        $this->assertDatabaseHas('addresses', ['id' => $remainingAddressId, 'is_default' => 1]);
    }

    public function test_customer_cannot_update_or_delete_another_customers_address(): void
    {
        $owner = $this->createCustomer('address-owner@example.test');
        $otherCustomer = $this->createCustomer('address-visitor@example.test');
        $addressId = $this->createAddress($owner->id, 'Địa chỉ riêng');

        $this->actingAs($otherCustomer)
            ->put(route('account.addresses.update', $addressId), $this->addressDetails())
            ->assertNotFound();

        $this->delete(route('account.addresses.destroy', $addressId))
            ->assertNotFound();

        $this->assertDatabaseHas('addresses', [
            'id' => $addressId,
            'user_id' => $owner->id,
            'receiver_name' => 'Địa chỉ riêng',
        ]);
    }

    public function test_guest_is_redirected_to_login_before_managing_saved_addresses(): void
    {
        $this->post(route('account.addresses.store'), $this->addressDetails())
            ->assertRedirectToRoute('login');
    }

    public function test_address_book_is_available_from_the_shared_account_navigation(): void
    {
        $user = $this->createCustomer();

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Sổ địa chỉ')
            ->assertSee(route('account.addresses.store'), false)
            ->assertSee('name="receiver_name"', false);
    }

    private function createAddress(int $userId, string $receiverName, bool $isDefault = false): int
    {
        return DB::table('addresses')->insertGetId([
            'user_id' => $userId,
            'receiver_name' => $receiverName,
            'phone' => '0900000000',
            'province' => 'Hà Nội',
            'district' => 'Ba Đình',
            'ward' => 'Phúc Xá',
            'detail_address' => 'Số 1 phố Hàng',
            'is_default' => $isDefault,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function addressDetails(): array
    {
        return [
            'receiver_name' => 'Nguyễn An',
            'phone' => '0900000000',
            'province' => 'Hà Nội',
            'district' => 'Ba Đình',
            'ward' => 'Phúc Xá',
            'detail_address' => 'Số 1 phố Hàng',
        ];
    }
}
