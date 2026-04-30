<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run: php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,   // 1 SuperAdmin
            AdminSeeder::class,        // 3 Company Admins
            UserSeeder::class,         // 5 Job Seekers
            JobCategorySeeder::class,  // 5 Categories
            JobRoleSeeder::class,      // 10 Roles
            JobSeeder::class,          // 12 Jobs
            UserProfileSeeder::class,  // 5 User Profiles
            JobApplicationSeeder::class, // 10 Applications
            FaqSeeder::class,          // 6 FAQs
        ]);
    }
}
