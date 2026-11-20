<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('categories')
            ->pluck('id', 'name');

        $brands = DB::table('brands')
            ->pluck('id', 'name');

        $suppliers = DB::table('suppliers')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $products = [
            ['Bút bi TL-027', 'Bút viết', 'Thiên Long', 5000],
            ['Bút bi TL-08', 'Bút viết', 'Thiên Long', 6000],
            ['Bút gel Deli', 'Bút viết', 'Deli', 12000],
            ['Bút chì Pentel', 'Bút viết', 'Pentel', 15000],
            ['Bút máy Pilot', 'Bút viết', 'Pilot', 85000],

            ['Vở kẻ ngang 200 trang', 'Vở và sổ', 'Thiên Long', 18000],
            ['Sổ tay lò xo A5', 'Vở và sổ', 'Deli', 35000],
            ['Sổ bìa da A5', 'Vở và sổ', 'Deli', 65000],

            ['Giấy in Double A A4', 'Giấy in', 'Double A', 85000],
            ['Giấy in A3', 'Giấy in', 'Double A', 160000],

            ['Thước kẻ 20cm', 'Dụng cụ học tập', 'Thiên Long', 8000],
            ['Compa học sinh', 'Dụng cụ học tập', 'Deli', 18000],

            ['Kéo văn phòng', 'Dụng cụ văn phòng', 'Deli', 25000],
            ['Bấm kim số 10', 'Dụng cụ văn phòng', 'Deli', 35000],

            ['Bìa hồ sơ A4', 'File và bìa hồ sơ', 'Deli', 12000],
            ['File lá A4', 'File và bìa hồ sơ', 'Deli', 25000],

            ['Máy tính Casio văn phòng', 'Thiết bị văn phòng', 'Deli', 250000],

            ['Bút màu 12 màu', 'Dụng cụ mỹ thuật', 'Thiên Long', 45000],

            ['Keo khô Deli', 'Keo và băng dính', 'Deli', 15000],

            ['Khay đựng bút để bàn', 'Phụ kiện bàn làm việc', 'Deli', 55000],
        ];

        $now = now();

        foreach ($products as $index => $item) {
            [$name, $categoryName, $brandName, $price] = $item;

            $productId = DB::table('products')->insertGetId([
                'category_id' => $categories->get($categoryName) ?? $categories->first(),
                'brand_id' => $brands->get($brandName) ?? $brands->first(),
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'Sản phẩm văn phòng phẩm '
                    .$name.' tai Stationery Store',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $variantId = DB::table('product_variants')
                ->insertGetId([
                    'product_id' => $productId,
                    'sku' => 'STS-'.str_pad(
                        $index + 1,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),
                    'variant_name' => 'Mặc định',
                    'attributes' => json_encode([
                        'type' => 'standard',
                    ]),
                    'price' => $price,
                    'compare_price' => null,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            DB::table('product_images')->insert([
                'product_id' => $productId,
                'image_url' => 'images/products/product-'
                    .($index + 1).'.jpg',
                'is_primary' => 1,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('inventory')->insert([
                'variant_id' => $variantId,
                'quantity' => 100 + ($index * 5),
                'reserved_quantity' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $supplierId = $suppliers[$index % count($suppliers)];

            DB::table('supplier_products')->insert([
                'supplier_id' => $supplierId,
                'variant_id' => $variantId,
                'import_price' => $price * 0.7,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('inventory_transactions')->insert([
                'variant_id' => $variantId,
                'supplier_id' => $supplierId,
                'order_id' => null,
                'transaction_type' => 'IMPORT',
                'quantity' => 100 + ($index * 5),
                'note' => 'Nhap hang ban dau',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
