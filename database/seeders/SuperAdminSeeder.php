<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        if (Admin::where('role', 'super_admin')->exists()) {
            return;
        }

        Admin::create([
            'email' => 'samad@gmail.com',
            'password' => Hash::make('123456'),
            'company_name' => 'Platform Owner',
            'role' => 'super_admin',
        ]);
    }
}
