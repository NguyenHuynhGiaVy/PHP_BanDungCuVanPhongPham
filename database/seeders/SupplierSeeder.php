<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Cong ty Van phong pham Thien Long',
                'email' => 'supplier1@example.com',
                'phone' => '02838000001',
                'address' => 'TP Ho Chi Minh',
            ],
            [
                'name' => 'Deli Vietnam',
                'email' => 'supplier2@example.com',
                'phone' => '02838000002',
                'address' => 'TP Ho Chi Minh',
            ],
            [
                'name' => 'Pentel Vietnam',
                'email' => 'supplier3@example.com',
                'phone' => '02838000003',
                'address' => 'TP Ho Chi Minh',
            ],
            [
                'name' => 'Pilot Vietnam',
                'email' => 'supplier4@example.com',
                'phone' => '02838000004',
                'address' => 'TP Ho Chi Minh',
            ],
            [
                'name' => 'Double A Vietnam',
                'email' => 'supplier5@example.com',
                'phone' => '02838000005',
                'address' => 'TP Ho Chi Minh',
            ],
        ];

        $now = now();

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->insert([
                ...$supplier,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
