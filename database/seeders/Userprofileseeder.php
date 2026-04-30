<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProfileSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_profiles')->delete();

        $users = DB::table('users')
            ->whereIn('email', [
                'samad@jobhub.com',
                'rahul@jobhub.com',
                'priya@jobhub.com',
                'amit@jobhub.com',
                'neha@jobhub.com',
            ])
            ->pluck('id', 'email');

        $profiles = [
            [
                'user_id' => $users['samad@jobhub.com'],
                'professional_summary' => 'Passionate PHP/Laravel developer with hands-on experience '
                                        .'building full-featured web applications. Skilled in REST API '
                                        .'development, MySQL database design, and clean code practices. '
                                        .'BCA final year student with strong self-learning ability.',
                'core_skills' => 'PHP, Laravel, MySQL, REST API, Git, Bootstrap, JavaScript, PHPUnit',
                'education' => [
                    ['degree' => 'BCA', 'institute' => 'Swami Vivekanand Subharti University, Meerut', 'year' => '2025', 'percentage' => '72%'],
                    ['degree' => '12th (PCM)', 'institute' => 'St. Mary\'s Inter College, Saharanpur', 'year' => '2022', 'percentage' => '68%'],
                ],
                'experience' => [
                    ['company' => 'Self-Project', 'role' => 'Laravel Developer', 'duration' => 'Jan 2025 - Present', 'description' => 'Built Job Portal with multi-role auth, REST API, and interview scheduling.'],
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users['rahul@jobhub.com'],
                'professional_summary' => 'Full Stack Developer with 1.5 years of experience in '
                                        .'React.js and Laravel. Built and deployed 3 production applications. '
                                        .'Strong problem-solver with a passion for clean UI and backend architecture.',
                'core_skills' => 'React.js, Laravel, Node.js, MySQL, MongoDB, Docker, AWS, Git',
                'education' => [
                    ['degree' => 'B.Tech (CSE)', 'institute' => 'AKTU, Noida', 'year' => '2023', 'percentage' => '78%'],
                ],
                'experience' => [
                    ['company' => 'StartupXYZ', 'role' => 'Junior Full Stack Developer', 'duration' => 'Aug 2023 - Dec 2024', 'description' => 'Built React dashboards and Laravel APIs for an e-commerce SaaS platform.'],
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users['priya@jobhub.com'],
                'professional_summary' => 'UI/UX Designer with 2 years of experience creating '
                                        .'user-centered designs for web and mobile applications. '
                                        .'Proficient in Figma and Adobe XD with a strong portfolio of 15+ projects.',
                'core_skills' => 'Figma, Adobe XD, Adobe Photoshop, Illustrator, User Research, Prototyping',
                'education' => [
                    ['degree' => 'B.Des (Visual Communication)', 'institute' => 'NID Ahmedabad', 'year' => '2022', 'percentage' => '81%'],
                ],
                'experience' => [
                    ['company' => 'Creative Agency Ahmedabad', 'role' => 'UI/UX Designer', 'duration' => 'Jul 2022 - Present', 'description' => 'Designed UI for 10+ client apps including a banking app and e-commerce platform.'],
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users['amit@jobhub.com'],
                'professional_summary' => 'Backend Developer with expertise in Node.js and Python. '
                                        .'3 years of experience building scalable microservices and APIs. '
                                        .'Strong knowledge of cloud infrastructure and DevOps practices.',
                'core_skills' => 'Node.js, Python, Django, MongoDB, Redis, Docker, Kubernetes, AWS',
                'education' => [
                    ['degree' => 'M.Tech (Software Engineering)', 'institute' => 'IIT Hyderabad', 'year' => '2021', 'percentage' => '8.2 CGPA'],
                ],
                'experience' => [
                    ['company' => 'TechSolutions Hyderabad', 'role' => 'Backend Developer', 'duration' => 'Jun 2021 - Present', 'description' => 'Built microservices handling 1M+ daily requests using Node.js and MongoDB.'],
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $users['neha@jobhub.com'],
                'professional_summary' => 'Digital Marketing Executive with 1 year of hands-on experience '
                                        .'in Google Ads, Meta Ads, and SEO. Managed ad budgets of ₹5L+ monthly. '
                                        .'Data-driven marketer with strong content creation skills.',
                'core_skills' => 'Google Ads, Meta Ads, SEO, Google Analytics, Content Writing, Canva',
                'education' => [
                    ['degree' => 'MBA (Marketing)', 'institute' => 'Delhi School of Economics', 'year' => '2023', 'percentage' => '74%'],
                    ['degree' => 'B.Com', 'institute' => 'Miranda House, Delhi University', 'year' => '2021', 'percentage' => '79%'],
                ],
                'experience' => [
                    ['company' => 'DigiMarketing India', 'role' => 'Digital Marketing Intern → Executive', 'duration' => 'Aug 2023 - Present', 'description' => 'Managed Google and Meta campaigns for 8 clients achieving average 3.5x ROAS.'],
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($profiles as $profile) {
            UserProfile::create($profile);
        }
    }
}
