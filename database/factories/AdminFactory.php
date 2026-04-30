<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'contact_number' => fake()->phoneNumber(),
            'location' => fake()->city(),
            'description' => fake()->sentence(),
            'role' => 'admin',
            'is_active' => true,
        ];
    }

    // Suspended admin
    public function suspended(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
