<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\JobCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobRole>
 */
class JobRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Developer', 'Designer', 'Manager', 'Analyst', 'Tester',
            ]),
            'admin_id' => Admin::factory(),
            'category_id' => JobCategory::factory(),
        ];
    }
}
