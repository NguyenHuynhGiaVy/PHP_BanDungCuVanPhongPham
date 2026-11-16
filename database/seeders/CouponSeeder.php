<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $coupons = [
            [
                'code' => 'WELCOME10',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_value' => 100000,
                'max_discount' => 30000,
            ],
            [
                'code' => 'SAVE20',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_order_value' => 200000,
                'max_discount' => 50000,
            ],
            [
                'code' => 'GIAM15K',
                'discount_type' => 'fixed',
                'discount_value' => 15000,
                'min_order_value' => 100000,
                'max_discount' => 15000,
            ],
            [
                'code' => 'OFF30K',
                'discount_type' => 'fixed',
                'discount_value' => 30000,
                'min_order_value' => 300000,
                'max_discount' => 30000,
            ],
            [
                'code' => 'STATIONERY',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'min_order_value' => 50000,
                'max_discount' => 20000,
            ],
        ];

        foreach ($coupons as $coupon) {
            DB::table('coupons')->insert([
                ...$coupon,
                'usage_limit' => 100,
                'used_count' => 0,
                'start_date' => $now,
                'end_date' => $now->copy()->addMonths(3),
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
