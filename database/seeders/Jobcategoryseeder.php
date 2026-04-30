<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('job_categories')->delete();

        // Get first admin (TechCorp) id
        $adminId = DB::table('admins')
            ->where('role', 'admin')
            ->value('id');

        if (! $adminId) {
            $this->command->error('No admin found. Run AdminSeeder first.');

            return;
        }

        $categories = [
            [
                'name' => 'Information Technology',
                'description' => 'Software development, web, mobile, cloud, DevOps, '
                               .'cybersecurity, and all IT-related roles.',
                'admin_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance & Banking',
                'description' => 'Accounting, financial analysis, banking operations, '
                               .'investment, and fintech roles.',
                'admin_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Design & Creative',
                'description' => 'UI/UX design, graphic design, branding, '
                               .'illustration, and animation roles.',
                'admin_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing & Sales',
                'description' => 'Digital marketing, SEO, content writing, '
                               .'social media, and business development roles.',
                'admin_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Recruitment, talent acquisition, HR operations, '
                               .'payroll, and training & development.',
                'admin_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('job_categories')->insert($categories);
        $this->command->info('Job Categories seeded: 5 categories');
    }
}
