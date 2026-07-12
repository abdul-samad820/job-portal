<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run: php artisan db:seed
     *
     * Only the SuperAdmin account is seeded here — every real
     * platform install needs at least one SuperAdmin to log in and
     * start approving/creating companies. Demo/dummy data (sample
     * companies, jobs, users, applications, etc.) is no longer seeded
     * from here — that's now a separate SQL dump (job_hub_seed_data.sql)
     * you can import only when you actually want a demo dataset, e.g.
     * for local development or a staging environment.
     */
    public function run(): void
    {
        if (app()->environment('production') && ! env('ALLOW_PROD_SEEDING')) {
            abort(403, 'Seeding is disabled in production.');
        }

        $this->call([
            SuperAdminSeeder::class,   // 1 SuperAdmin
        ]);
    }
}
