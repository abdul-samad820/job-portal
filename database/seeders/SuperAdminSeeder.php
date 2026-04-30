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

        DB::table('admins')->insert([
            'company_name' => 'JobHub Platform',
            'email' => 'superadmin@jobhub.com',
            'password' => Hash::make('SuperAdmin@123'),
            'contact_number' => '9000000001',
            'location' => 'New Delhi, India',
            'description' => 'JobHub platform super administrator with full access.',
            'expertise' => 'Platform Management, User Administration',
            'role' => 'super_admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info(' SuperAdmin seeded: superadmin@jobhub.com / SuperAdmin@123');
    }
}
