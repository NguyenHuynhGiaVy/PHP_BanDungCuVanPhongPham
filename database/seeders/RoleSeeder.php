<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->insert([
            [
                'name' => 'ADMIN',
                'description' => 'Quan tri vien he thong',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'CUSTOMER',
                'description' => 'Khach hang mua sam',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
