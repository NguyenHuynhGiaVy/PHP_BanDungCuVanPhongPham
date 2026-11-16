<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_customer_can_save_a_product_once_and_remove_it_from_wishlist(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();

        $this->actingAs($user)
            ->post(route('wishlist.store', $product['product_id']))
            ->assertRedirect();
        $this->post(route('wishlist.store', $product['product_id']))
            ->assertRedirect();

        $this->assertDatabaseCount('wishlists', 1);
        $response = $this->get(route('wishlist.index'));
        $response->assertOk();
        $response->assertSee('Sản phẩm thử nghiệm');

        $wishlistId = DB::table('wishlists')->value('id');
        $this->delete(route('wishlist.destroy', $wishlistId))->assertRedirect();
        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_customer_cannot_remove_another_customers_wishlist_entry(): void
    {
        $owner = $this->createCustomer('wishlist-owner@example.test');
        $otherCustomer = $this->createCustomer('wishlist-visitor@example.test');
        $product = $this->createProductRecord();
        $wishlistId = DB::table('wishlists')->insertGetId([
            'user_id' => $owner->id,
            'product_id' => $product['product_id'],
            'created_at' => now(),
        ]);

        $this->actingAs($otherCustomer)
            ->delete(route('wishlist.destroy', $wishlistId))
            ->assertNotFound();

        $this->assertDatabaseHas('wishlists', ['id' => $wishlistId, 'user_id' => $owner->id]);
    }
}
