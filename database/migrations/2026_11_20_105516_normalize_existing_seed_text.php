<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->where('email', 'admin@stationery.com')->update(['full_name' => 'Quản trị viên']);
        DB::table('users')->where('email', 'customer1@gmail.com')->update(['full_name' => 'Nguyễn Văn An']);
        DB::table('users')->where('email', 'customer2@gmail.com')->update(['full_name' => 'Trần Thị Bình']);

        DB::table('suppliers')->where('name', 'Cong ty Van phong pham Thien Long')
            ->update([
                'name' => 'Công ty Văn phòng phẩm Thiên Long',
                'address' => 'TP. Hồ Chí Minh',
            ]);

        DB::table('product_variants')->where('variant_name', 'Mac dinh')->update([
            'variant_name' => 'Mặc định',
        ]);

        DB::table('categories')->where('description', 'like', 'Danh muc %')
            ->update(['description' => DB::raw("replace(description, 'Danh muc ', 'Danh mục ')")]);

        DB::table('products')->where('description', 'like', 'San pham van phong pham %')
            ->update(['description' => DB::raw("replace(description, 'San pham van phong pham ', 'Sản phẩm văn phòng phẩm ')")]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
