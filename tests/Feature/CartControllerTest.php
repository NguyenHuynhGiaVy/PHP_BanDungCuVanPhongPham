<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesCustomerCommerceRecords;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use CreatesCustomerCommerceRecords;
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_before_accessing_cart(): void
    {
        $this->get(route('cart.index'))
            ->assertRedirectToRoute('login');
    }

    public function test_customer_can_add_and_update_a_variant_within_available_stock(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5, 1);

        $this->actingAs($user)
            ->post(route('cart.items.store'), ['variant_id' => $variantId, 'quantity' => 2])
            ->assertRedirectToRoute('cart.index');

        $cartId = DB::table('carts')->where('user_id', $user->id)->value('id');
        $cartItemId = DB::table('cart_items')->where('cart_id', $cartId)->value('id');
        $this->assertDatabaseHas('cart_items', ['id' => $cartItemId, 'quantity' => 2]);

        $this->put(route('cart.items.update', $cartItemId), ['quantity' => 3])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['id' => $cartItemId, 'quantity' => 3]);
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('class="store-cart-count" aria-hidden="true">3</span>', false);
    }

    public function test_customer_can_add_a_variant_and_continue_to_checkout(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5);

        $this->actingAs($user)
            ->post(route('cart.items.store'), [
                'variant_id' => $variantId,
                'quantity' => 2,
                'purchase_intent' => 'checkout',
            ])
            ->assertRedirectToRoute('checkout.create');

        $cartId = DB::table('carts')->where('user_id', $user->id)->value('id');
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cartId,
            'variant_id' => $variantId,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_redirect_cart_addition_to_an_unapproved_destination(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5);

        $this->actingAs($user)
            ->post(route('cart.items.store'), [
                'variant_id' => $variantId,
                'quantity' => 1,
                'purchase_intent' => 'https://example.test',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('purchase_intent');

        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
    }

    public function test_cart_page_shows_product_image_quantity_controls_and_remove_confirmation(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5);
        DB::table('product_images')->insert([
            'product_id' => $product['product_id'],
            'image_url' => 'images/products/cart-item.jpg',
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
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('images/products/cart-item.jpg')
            ->assertSee('Thành tiền: 25.000 ₫')
            ->assertSee('data-quantity-step="-1"', false)
            ->assertSee('data-quantity-step="1"', false)
            ->assertSee('data-cart-remove-modal', false)
            ->assertSee('data-cart-remove-confirm', false);
    }

    public function test_customer_cannot_add_more_than_available_stock(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 2, 1);

        $response = $this->actingAs($user)
            ->post(route('cart.items.store'), ['variant_id' => $variantId, 'quantity' => 2]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
    }

    public function test_customer_cannot_change_another_customers_cart_item(): void
    {
        $owner = $this->createCustomer('owner@example.test');
        $otherCustomer = $this->createCustomer('other@example.test');
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5);
        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $owner->id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cartItemId = DB::table('cart_items')->insertGetId([
            'cart_id' => $cartId,
            'variant_id' => $variantId,
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($otherCustomer)
            ->put(route('cart.items.update', $cartItemId), ['quantity' => 4])
            ->assertNotFound();

        $this->assertDatabaseHas('cart_items', ['id' => $cartItemId, 'quantity' => 1]);
    }

    public function test_customer_can_remove_a_cart_item(): void
    {
        $user = $this->createCustomer();
        $product = $this->createProductRecord();
        $variantId = $this->createVariantRecord($product['product_id']);
        $this->createInventoryRecord($variantId, 5);
        $cartId = DB::table('carts')->insertGetId([
            'user_id' => $user->id,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cartItemId = DB::table('cart_items')->insertGetId([
            'cart_id' => $cartId,
            'variant_id' => $variantId,
            'quantity' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('cart.items.destroy', $cartItemId))
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItemId]);
    }
}
