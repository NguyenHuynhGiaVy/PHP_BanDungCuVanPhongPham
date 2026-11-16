<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Bìa hồ sơ',
            'Bút ký',
            'Bút bi',
            'Sổ tay và sổ ghi chép',
            'Băng keo và keo dán',
            'Bảng tên và dây đeo thẻ',
            'Bút chì gỗ',
            'Hóa đơn',
            'Giấy in và giấy photo',
            'Bấm ghim và kẹp giấy',
            'Máy tính',
            'Thước',
            'Bút lông',
            'Kệ tài liệu',
            'Hộp dấu',
            'Tập vở học sinh',
            'Dao rọc giấy',
            'Dụng cụ khác',
        ];

        $now = now();

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'parent_id' => null,
                'name' => $category,
                'slug' => Str::slug($category),
                'description' => 'Danh muc ' . $category
                    . ' tai Stationery Store',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
