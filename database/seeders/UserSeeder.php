<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $adminRole = DB::table('roles')
            ->where('name', 'ADMIN')
            ->value('id');

        $customerRole = DB::table('roles')
            ->where('name', 'CUSTOMER')
            ->value('id');

        DB::table('users')->insert([
            [
                'role_id' => $adminRole,
                'full_name' => 'Administrator',
                'email' => 'admin@stationery.com',
                'password' => Hash::make('Admin@123'),
                'phone' => '0900000001',
                'avatar' => null,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => $customerRole,
                'full_name' => 'Nguyen Van An',
                'email' => 'customer1@gmail.com',
                'password' => Hash::make('Customer@123'),
                'phone' => '0900000002',
                'avatar' => null,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'role_id' => $customerRole,
                'full_name' => 'Tran Thi Binh',
                'email' => 'customer2@gmail.com',
                'password' => Hash::make('Customer@123'),
                'phone' => '0900000003',
                'avatar' => null,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
