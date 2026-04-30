<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\JobCategory;
use App\Models\JobRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(2, true),
            'location' => fake()->city(),
            'min_salary' => fake()->numberBetween(20000, 50000),
            'max_salary' => fake()->numberBetween(50000, 150000),
            'type' => fake()->randomElement([
                'Full-time', 'Part-time', 'Internship', 'Contract',
            ]),
            'experience' => fake()->randomElement([
                'Fresher', '1 Year', '2 Years', '3 Years', '3+ Years',
            ]),
            'required_skills' => 'PHP, Laravel, MySQL',
            'last_date' => now()->addDays(30)->format('Y-m-d'),
            'admin_id' => Admin::factory(),
            'category_id' => JobCategory::factory(),
            'role_id' => JobRole::factory(),
        ];
    }

    // Expired job
    public function expired(): static
    {
        return $this->state(fn () => [
            'last_date' => now()->subDays(5)->format('Y-m-d'),
        ]);
    }
}
