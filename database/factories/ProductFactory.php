<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'category_id' => Category::query()
                ->inRandomOrder()
                ->value('id'),

            'brand_id' => Brand::query()
                ->inRandomOrder()
                ->value('id'),

            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->paragraph(),
            'status' => 1,
        ];
    }
}
