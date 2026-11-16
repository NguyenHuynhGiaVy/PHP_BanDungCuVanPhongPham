<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role_id' => Role::where(
                'name',
                'CUSTOMER'
            )->value('id'),

            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('Customer@123'),
            'phone' => fake()->numerify('09########'),
            'avatar' => null,
            'status' => 1,
            'remember_token' => Str::random(10),
        ];
    }
}
