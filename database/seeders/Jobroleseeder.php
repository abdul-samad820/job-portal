<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('job_roles')->delete();

        $adminId = DB::table('admins')->where('role', 'admin')->value('id');

        // Get category IDs
        $it = DB::table('job_categories')->where('name', 'Information Technology')->value('id');
        $finance = DB::table('job_categories')->where('name', 'Finance & Banking')->value('id');
        $design = DB::table('job_categories')->where('name', 'Design & Creative')->value('id');
        $mktg = DB::table('job_categories')->where('name', 'Marketing & Sales')->value('id');
        $hr = DB::table('job_categories')->where('name', 'Human Resources')->value('id');

        $roles = [
            // ── IT ──────────────────────────────────────────
            [
                'category_id' => $it,
                'admin_id' => $adminId,
                'name' => 'PHP / Laravel Developer',
                'description' => 'Build and maintain web applications using PHP and Laravel framework.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'category_id' => $it,
                'admin_id' => $adminId,
                'name' => 'Full Stack Developer',
                'description' => 'Work on both frontend (React/Vue) and backend (Laravel/Node) development.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'category_id' => $it,
                'admin_id' => $adminId,
                'name' => 'Backend Developer',
                'description' => 'Design APIs, databases, and server-side logic for web applications.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'category_id' => $it,
                'admin_id' => $adminId,
                'name' => 'DevOps Engineer',
                'description' => 'Manage CI/CD pipelines, cloud infrastructure, and deployment processes.',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ── Finance ──────────────────────────────────────
            [
                'category_id' => $finance,
                'admin_id' => $adminId,
                'name' => 'Financial Analyst',
                'description' => 'Analyze financial data, prepare reports, and support business decisions.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'category_id' => $finance,
                'admin_id' => $adminId,
                'name' => 'Accountant',
                'description' => 'Manage accounts, ledgers, GST filings, and financial compliance.',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ── Design ──────────────────────────────────────
            [
                'category_id' => $design,
                'admin_id' => $adminId,
                'name' => 'UI/UX Designer',
                'description' => 'Design user interfaces, wireframes, and interactive prototypes.',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'category_id' => $design,
                'admin_id' => $adminId,
                'name' => 'Graphic Designer',
                'description' => 'Create visual content for digital and print media.',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ── Marketing ───────────────────────────────────
            [
                'category_id' => $mktg,
                'admin_id' => $adminId,
                'name' => 'Digital Marketing Executive',
                'description' => 'Run SEO, Google Ads, and social media campaigns.',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ── HR ──────────────────────────────────────────
            [
                'category_id' => $hr,
                'admin_id' => $adminId,
                'name' => 'HR Recruiter',
                'description' => 'Source, screen, and recruit candidates for various roles.',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        DB::table('job_roles')->insert($roles);
        $this->command->info(' Job Roles seeded: 10 roles across 5 categories');
    }
}
