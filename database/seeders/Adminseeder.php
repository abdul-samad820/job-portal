<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->where('role', 'admin')->delete();

        $admins = [
            [
                'company_name' => 'TechCorp India Pvt Ltd',
                'email' => 'hr@techcorp.com',
                'password' => Hash::make('Admin@123'),
                'contact_number' => '9000000002',
                'location' => 'Bengaluru, Karnataka',
                'description' => 'Leading IT solutions company with 500+ employees. '
                                  .'Specializing in web, mobile, and cloud technologies. '
                                  .'Founded in 2010, serving clients globally.',
                'expertise' => 'PHP, Laravel, React, Node.js, AWS, DevOps',
                'role' => 'admin',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'company_name' => 'FinanceHub Solutions',
                'email' => 'careers@financehub.com',
                'password' => Hash::make('Admin@123'),
                'contact_number' => '9000000003',
                'location' => 'Mumbai, Maharashtra',
                'description' => 'Top-rated fintech company providing banking and '
                                  .'financial software solutions. 200+ team members '
                                  .'working across India and Southeast Asia.',
                'expertise' => 'Finance, Accounting, Data Analysis, FinTech',
                'role' => 'admin',
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ],
            [
                'company_name' => 'DesignStudio Creative Agency',
                'email' => 'jobs@designstudio.com',
                'password' => Hash::make('Admin@123'),
                'contact_number' => '9000000004',
                'location' => 'Pune, Maharashtra',
                'description' => 'Award-winning design agency specializing in UI/UX, '
                                  .'branding, and digital marketing. Worked with '
                                  .'100+ brands across India and abroad.',
                'expertise' => 'UI/UX Design, Branding, Figma, Adobe Suite',
                'role' => 'admin',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
        ];

        DB::table('admins')->insert($admins);

        $this->command->info('Admins seeded:');
        $this->command->line('   hr@techcorp.com       / Admin@123');
        $this->command->line('   careers@financehub.com / Admin@123');
        $this->command->line('   jobs@designstudio.com  / Admin@123');
    }
}
