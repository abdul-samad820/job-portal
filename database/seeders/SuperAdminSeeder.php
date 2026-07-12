<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing superadmin to avoid duplicates
        DB::table('admins')->where('role', 'super_admin')->delete();

        $password = env('SEED_SUPERADMIN_PASSWORD', 'SuperAdmin@123');

        DB::table('admins')->insert([
            'company_name' => 'JobHub Platform',
            'email' => 'superadmin@jobhub.com',
            'password' => Hash::make($password),
            'contact_number' => '9000000001',
            'location' => 'New Delhi, India',
            'description' => 'JobHub platform super administrator with full access.',
            'expertise' => 'Platform Management, User Administration',
            'role' => 'super_admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Only echo credentials in local dev — never in CI/CD or production
        // logs. Set SEED_SUPERADMIN_PASSWORD in .env to override the default.
        if (app()->environment('local')) {
            $this->command->info(" SuperAdmin seeded: superadmin@jobhub.com / {$password}");
        } else {
            $this->command->info(' SuperAdmin seeded (credentials not printed outside local env).');
        }
    }
}
