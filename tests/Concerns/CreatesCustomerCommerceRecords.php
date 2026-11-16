<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait CreatesCustomerCommerceRecords
{
    protected function createCustomer(string $email = 'customer@example.test', array $attributes = []): User
    {
        $roleId = DB::table('roles')->where('name', 'CUSTOMER')->value('id');

        if ($roleId === null) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'CUSTOMER',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return User::query()->create([
            'role_id' => $roleId,
            'full_name' => 'Khách hàng thử nghiệm',
            'email' => $email,
            'password' => Hash::make('password-for-test'),
            'status' => 1,
            ...$attributes,
        ]);
    }

    /**
     * @return array{category_id: int, brand_id: int, product_id: int}
     */
    protected function createProductRecord(string $slug = 'san-pham-thu-nghiem', array $attributes = []): array
    {
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Văn phòng phẩm',
            'slug' => 'van-phong-pham-'.$slug,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $brandId = DB::table('brands')->insertGetId([
            'name' => 'Thương hiệu thử nghiệm',
            'slug' => 'thuong-hieu-'.$slug,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'name' => 'Sản phẩm thử nghiệm',
            'slug' => $slug,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ...$attributes,
        ]);

        return [
            'category_id' => $categoryId,
            'brand_id' => $brandId,
            'product_id' => $productId,
        ];
    }

    protected function createVariantRecord(int $productId, string $sku = 'SKU-TEST-001', int $price = 12500): int
    {
        return DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'sku' => $sku,
            'variant_name' => 'Mẫu tiêu chuẩn',
            'price' => $price,
            'compare_price' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createInventoryRecord(int $variantId, int $quantity, int $reservedQuantity = 0): void
    {
        DB::table('inventory')->insert([
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'reserved_quantity' => $reservedQuantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
