<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StoreLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_shows_active_database_categories(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Bút viết',
                'slug' => 'but-viet',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Danh mục ẩn',
                'slug' => 'danh-muc-an',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Bút viết');
        $response->assertDontSee('Danh mục ẩn');
        $response->assertSee('data-bs-toggle="collapse"', false);
        $response->assertSee('store-mega-menu-heading', false);
        $response->assertSee('1 danh mục từ cửa hàng');
        $response->assertSee(route('products.category', 'but-viet'), false);
        $response->assertSee('class="store-sticky-header"', false);
        $response->assertSee('Khám phá cửa hàng');
        $response->assertSee('Hỗ trợ khách hàng');
        $response->assertSee('Hotline: đang cập nhật');
    }

    public function test_store_layout_uses_the_stationery_store_wordmark_in_header_and_footer(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Stationery <span>Store</span>', false);
        $response->assertSee('Văn phòng phẩm cho mọi ngày');
        $response->assertSee('<svg viewBox="0 0 48 48"', false);
        $response->assertSee('aria-label="Giỏ hàng, 0 sản phẩm"', false);
    }
}
