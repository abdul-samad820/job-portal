<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'job_id' => Job::factory(),
            'cover_letter' => fake()->paragraph(),
            'resume' => 'resumes/test_resume.pdf',
            'status' => 'pending',
            'expected_salary' => '40000',
            'notice_period' => 'Immediate',
        ];
    }

    public function shortlisted(): static
    {
        return $this->state(fn () => ['status' => 'shortlisted']);
    }

    public function hired(): static
    {
        return $this->state(fn () => ['status' => 'hired']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
