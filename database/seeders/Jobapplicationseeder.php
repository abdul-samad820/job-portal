<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('job_applications')->delete();

        // Users
        $samad = DB::table('users')->where('email', 'samad@jobhub.com')->value('id');
        $rahul = DB::table('users')->where('email', 'rahul@jobhub.com')->value('id');
        $priya = DB::table('users')->where('email', 'priya@jobhub.com')->value('id');
        $amit = DB::table('users')->where('email', 'amit@jobhub.com')->value('id');
        $neha = DB::table('users')->where('email', 'neha@jobhub.com')->value('id');

        // Jobs
        $laravelJob = DB::table('jobs')->where('title', 'Laravel Developer')->value('id');
        $phpJob = DB::table('jobs')->where('title', 'PHP Developer (Fresher)')->value('id');
        $fsJob = DB::table('jobs')->where('title', 'Full Stack Developer (React + Laravel)')->value('id');
        $contractJob = DB::table('jobs')->where('title', 'Laravel Developer (Contract)')->value('id');
        $nodeJob = DB::table('jobs')->where('title', 'Backend Developer (Node.js)')->value('id');
        $uxJob = DB::table('jobs')->where('title', 'UI/UX Designer')->value('id');
        $gdJob = DB::table('jobs')->where('title', 'Graphic Designer')->value('id');
        $dmJob = DB::table('jobs')->where('title', 'Digital Marketing Executive')->value('id');
        $faJob = DB::table('jobs')->where('title', 'Senior Financial Analyst')->value('id');
        $devopsJob = DB::table('jobs')->where('title', 'DevOps Engineer')->value('id');

        // Admin IDs for status_updated_by
        $techcorp = DB::table('admins')->where('email', 'hr@techcorp.com')->value('id');
        $design = DB::table('admins')->where('email', 'jobs@designstudio.com')->value('id');

        $applications = [
            // Samad — applied for Laravel jobs (main user)
            [
                'user_id' => $samad,
                'job_id' => $laravelJob,
                'cover_letter' => 'I am a BCA final year student with hands-on Laravel experience. '
                                      .'I have built a full-featured Job Portal with multi-role auth, '
                                      .'REST API, interview scheduling, and 25 PHPUnit tests. '
                                      .'I am confident I can contribute to your team from day one.',
                'resume' => 'resumes/samad_resume.pdf',
                'status' => 'shortlisted',
                'expected_salary' => '50000',
                'notice_period' => 'Immediate',
                'admin_note' => 'Strong portfolio — schedule technical interview.',
                'status_updated_at' => now()->subDays(1),
                'updated_by_admin_id' => $techcorp,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $samad,
                'job_id' => $contractJob,
                'cover_letter' => 'I have experience migrating legacy code and writing clean '
                                      .'Laravel applications. I am available immediately and can '
                                      .'commit to the 6-month contract timeline.',
                'resume' => 'resumes/samad_resume.pdf',
                'status' => 'pending',
                'expected_salary' => '70000',
                'notice_period' => 'Immediate',
                'admin_note' => null,
                'status_updated_at' => null,
                'updated_by_admin_id' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],

            // Rahul — applied for Full Stack
            [
                'user_id' => $rahul,
                'job_id' => $fsJob,
                'cover_letter' => 'I have 1.5 years of experience with React.js and Laravel. '
                                      .'I have built 3 production applications and am comfortable '
                                      .'working in fast-paced environments. Excited about this role!',
                'resume' => 'resumes/rahul_resume.pdf',
                'status' => 'hired',
                'expected_salary' => '80000',
                'notice_period' => '30 Days',
                'admin_note' => 'Excellent technical skills. Offered position. Accepted.',
                'status_updated_at' => now()->subDays(2),
                'updated_by_admin_id' => $techcorp,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $rahul,
                'job_id' => $nodeJob,
                'cover_letter' => 'Although my primary stack is React+Laravel, I have '
                                      .'strong Node.js skills with 1 year of experience '
                                      .'building REST APIs with Express and MongoDB.',
                'resume' => 'resumes/rahul_resume.pdf',
                'status' => 'rejected',
                'expected_salary' => '100000',
                'notice_period' => '30 Days',
                'admin_note' => 'Looking for 3+ years Node.js specialist. Skill mismatch.',
                'status_updated_at' => now()->subDays(3),
                'updated_by_admin_id' => $techcorp,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(3),
            ],

            // Priya — applied for design jobs
            [
                'user_id' => $priya,
                'job_id' => $uxJob,
                'cover_letter' => 'I am a UI/UX Designer with 2 years of experience and '
                                      .'a portfolio of 15+ projects across FinTech and E-commerce. '
                                      .'My Figma skills and user research approach are my strengths.',
                'resume' => 'resumes/priya_resume.pdf',
                'status' => 'shortlisted',
                'expected_salary' => '65000',
                'notice_period' => '15 Days',
                'admin_note' => 'Very strong portfolio — schedule design test.',
                'status_updated_at' => now()->subDays(1),
                'updated_by_admin_id' => $design,
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $priya,
                'job_id' => $gdJob,
                'cover_letter' => 'While my primary focus is UX, I have strong graphic '
                                      .'design skills in Photoshop and Illustrator. '
                                      .'I would love to contribute to your creative team.',
                'resume' => 'resumes/priya_resume.pdf',
                'status' => 'pending',
                'expected_salary' => '45000',
                'notice_period' => '15 Days',
                'admin_note' => null,
                'status_updated_at' => null,
                'updated_by_admin_id' => null,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],

            // Amit — applied for backend/devops
            [
                'user_id' => $amit,
                'job_id' => $nodeJob,
                'cover_letter' => 'I have 3 years of Node.js experience building '
                                      .'microservices handling 1M+ daily requests. '
                                      .'Strong in MongoDB, Redis, Docker, and AWS EC2.',
                'resume' => 'resumes/amit_resume.pdf',
                'status' => 'shortlisted',
                'expected_salary' => '110000',
                'notice_period' => '60 Days',
                'admin_note' => 'Perfect profile match — schedule HR call.',
                'status_updated_at' => now()->subDays(1),
                'updated_by_admin_id' => $techcorp,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => $amit,
                'job_id' => $devopsJob,
                'cover_letter' => 'I have hands-on experience with Docker, Kubernetes, '
                                      .'and AWS. I manage CI/CD pipelines and have deployed '
                                      .'containerized applications in production.',
                'resume' => 'resumes/amit_resume.pdf',
                'status' => 'pending',
                'expected_salary' => '130000',
                'notice_period' => '60 Days',
                'admin_note' => null,
                'status_updated_at' => null,
                'updated_by_admin_id' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],

            // Neha — applied for marketing
            [
                'user_id' => $neha,
                'job_id' => $dmJob,
                'cover_letter' => 'I have 1 year of experience managing Google and Meta Ads '
                                      .'with monthly budgets of ₹5L+, achieving 3.5x ROAS. '
                                      .'I am data-driven, creative, and ready to grow your campaigns.',
                'resume' => 'resumes/neha_resume.pdf',
                'status' => 'pending',
                'expected_salary' => '45000',
                'notice_period' => '30 Days',
                'admin_note' => null,
                'status_updated_at' => null,
                'updated_by_admin_id' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],

            // PHP Fresher applications
            [
                'user_id' => $samad,
                'job_id' => $phpJob,
                'cover_letter' => 'As a BCA student passionate about PHP and Laravel, '
                                      .'I have already built a Job Portal project which demonstrates '
                                      .'my practical skills beyond classroom learning.',
                'resume' => 'resumes/samad_resume.pdf',
                'status' => 'hired',
                'expected_salary' => '30000',
                'notice_period' => 'Immediate',
                'admin_note' => 'Best fresher candidate. Offered training batch slot.',
                'status_updated_at' => now()->subDays(3),
                'updated_by_admin_id' => $techcorp,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(3),
            ],
        ];

        DB::table('job_applications')->insert($applications);
        $this->command->info('Job Applications seeded: 10 applications (hired/shortlisted/rejected/pending)');
    }
}
