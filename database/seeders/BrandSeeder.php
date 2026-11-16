<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Thiên Long',
            'Deli',
            'Pentel',
            'Pilot',
            'Double A',
            'PaperOne',
            'ERAS',
            'Casio',
        ];

        $now = now();

        foreach ($brands as $brand) {
            DB::table('brands')->insert([
                'name' => $brand,
                'slug' => Str::slug($brand),
                'logo' => null,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
