<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jobs')->delete();

        // Admins
        $techcorp = DB::table('admins')->where('email', 'hr@techcorp.com')->value('id');
        $financehub = DB::table('admins')->where('email', 'careers@financehub.com')->value('id');
        $designstudio = DB::table('admins')->where('email', 'jobs@designstudio.com')->value('id');

        // Categories
        $it = DB::table('job_categories')->where('name', 'Information Technology')->value('id');
        $finance = DB::table('job_categories')->where('name', 'Finance & Banking')->value('id');
        $design = DB::table('job_categories')->where('name', 'Design & Creative')->value('id');
        $mktg = DB::table('job_categories')->where('name', 'Marketing & Sales')->value('id');

        // Roles
        $phpRole = DB::table('job_roles')->where('name', 'PHP / Laravel Developer')->value('id');
        $fsRole = DB::table('job_roles')->where('name', 'Full Stack Developer')->value('id');
        $beRole = DB::table('job_roles')->where('name', 'Backend Developer')->value('id');
        $devopsRole = DB::table('job_roles')->where('name', 'DevOps Engineer')->value('id');
        $faRole = DB::table('job_roles')->where('name', 'Financial Analyst')->value('id');
        $accRole = DB::table('job_roles')->where('name', 'Accountant')->value('id');
        $uxRole = DB::table('job_roles')->where('name', 'UI/UX Designer')->value('id');
        $gdRole = DB::table('job_roles')->where('name', 'Graphic Designer')->value('id');
        $dmRole = DB::table('job_roles')->where('name', 'Digital Marketing Executive')->value('id');

        $jobs = [

            // ── TechCorp Jobs ────────────────────────────────
            [
                'admin_id' => $techcorp,
                'category_id' => $it,
                'role_id' => $phpRole,
                'title' => 'Laravel Developer',
                'description' => 'We are looking for a skilled Laravel developer to join our growing team. '
                                   .'You will be responsible for building and maintaining high-performance web applications.',
                'overview' => 'TechCorp India is hiring a Laravel Developer for its core product team. '
                                   .'You will work with a team of 5 developers on our flagship SaaS platform '
                                   .'serving 10,000+ users. This is a fully remote-friendly role.',
                'responsibilities' => "- Develop and maintain Laravel-based web applications\n"
                                   ."- Write clean, testable, and well-documented PHP code\n"
                                   ."- Design and optimize MySQL database schemas\n"
                                   ."- Build and consume RESTful APIs\n"
                                   ."- Collaborate with frontend developers using Git\n"
                                   .'- Participate in code reviews and sprint planning',
                'required_skills' => 'PHP, Laravel, MySQL, REST API, Git, Bootstrap, Blade',
                'location' => 'Bengaluru, Karnataka',
                'min_salary' => 500000,
                'max_salary' => 700000,
                'type' => 'Full-time',
                'experience' => '1 Year',
                'last_date' => now()->addDays(25)->format('Y-m-d'),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'admin_id' => $techcorp,
                'category_id' => $it,
                'role_id' => $fsRole,
                'title' => 'Full Stack Developer (React + Laravel)',
                'description' => 'Join our product team as a Full Stack Developer. '
                                   .'You will work on both the React frontend and Laravel backend of our platform.',
                'overview' => 'We are building a next-generation project management tool and need '
                                   .'a talented Full Stack developer who can bridge the gap between design '
                                   .'and backend. You will be the 6th member of our engineering team.',
                'responsibilities' => "- Build React components and pages from Figma designs\n"
                                   ."- Develop Laravel APIs consumed by the React frontend\n"
                                   ."- Manage state with Redux or React Query\n"
                                   ."- Write frontend unit tests with Jest\n"
                                   .'- Optimize application performance and loading speed',
                'required_skills' => 'React.js, Laravel, PHP, MySQL, REST API, Redux, Git, Tailwind CSS',
                'location' => 'Remote (India)',
                'min_salary' => 800000,
                'max_salary' => 1000000,
                'type' => 'Full-time',
                'experience' => '2 Years',
                'last_date' => now()->addDays(20)->format('Y-m-d'),
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'admin_id' => $techcorp,
                'category_id' => $it,
                'role_id' => $phpRole,
                'title' => 'PHP Developer (Fresher)',
                'description' => 'Exciting opportunity for fresh graduates who are passionate about '
                                   .'backend development. We provide full training and mentorship.',
                'overview' => 'TechCorp is running a 2025 Fresher Batch hiring program. '
                                   .'Selected candidates will go through a 2-month paid training '
                                   .'followed by joining the product team.',
                'responsibilities' => "- Learn and implement PHP/Laravel under senior guidance\n"
                                   ."- Work on assigned modules of the main product\n"
                                   ."- Write unit tests for your code\n"
                                   .'- Attend daily standups and weekly code reviews',
                'required_skills' => 'PHP, HTML, CSS, MySQL, Basic Laravel knowledge',
                'location' => 'Bengaluru, Karnataka',
                'min_salary' => 300000,
                'max_salary' => 400000,
                'type' => 'Full-time',
                'experience' => 'Fresher',
                'last_date' => now()->addDays(30)->format('Y-m-d'),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'admin_id' => $techcorp,
                'category_id' => $it,
                'role_id' => $beRole,
                'title' => 'Backend Developer (Node.js)',
                'description' => 'We need a Backend Developer with strong Node.js skills to build '
                                   .'scalable microservices for our enterprise clients.',
                'overview' => 'This role focuses on building high-throughput APIs using Node.js '
                                   .'and Express. You will work on real-time features using WebSockets '
                                   .'and manage data with MongoDB and Redis.',
                'responsibilities' => "- Build RESTful and WebSocket APIs with Node.js/Express\n"
                                   ."- Design MongoDB schemas and aggregation pipelines\n"
                                   ."- Implement Redis caching for performance\n"
                                   ."- Deploy services on AWS EC2 with Docker\n"
                                   .'- Write integration tests with Mocha/Chai',
                'required_skills' => 'Node.js, Express.js, MongoDB, Redis, Docker, AWS, REST API',
                'location' => 'Bengaluru, Karnataka',
                'min_salary' => 1100000,
                'max_salary' => 1300000,
                'type' => 'Full-time',
                'experience' => '3 Years',
                'last_date' => now()->addDays(18)->format('Y-m-d'),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'admin_id' => $techcorp,
                'category_id' => $it,
                'role_id' => $devopsRole,
                'title' => 'DevOps Engineer',
                'description' => 'Manage our cloud infrastructure on AWS, automate CI/CD pipelines, '
                                   .'and ensure zero-downtime deployments.',
                'overview' => 'As a DevOps Engineer at TechCorp, you will own our entire '
                                   .'infrastructure stack including AWS, Docker, Kubernetes, '
                                   .'and GitHub Actions CI/CD pipelines.',
                'responsibilities' => "- Manage AWS infrastructure (EC2, RDS, S3, CloudFront)\n"
                                   ."- Build and maintain CI/CD pipelines using GitHub Actions\n"
                                   ."- Containerize applications with Docker and Kubernetes\n"
                                   ."- Monitor system health with CloudWatch and Grafana\n"
                                   .'- Implement security best practices and compliance',
                'required_skills' => 'AWS, Docker, Kubernetes, CI/CD, Linux, Terraform, GitHub Actions',
                'location' => 'Remote (India)',
                'min_salary' => 1400000,
                'max_salary' => 1700000,
                'type' => 'Full-time',
                'experience' => '3+ Years',
                'last_date' => now()->addDays(22)->format('Y-m-d'),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],

            // ── FinanceHub Jobs ──────────────────────────────
            [
                'admin_id' => $financehub,
                'category_id' => $finance,
                'role_id' => $faRole,
                'title' => 'Senior Financial Analyst',
                'description' => 'Analyze financial statements, model business scenarios, '
                                   .'and present insights to senior leadership.',
                'overview' => 'FinanceHub is expanding its analytics team and needs a '
                                   .'Senior Financial Analyst to support our FinTech product '
                                   .'decisions with data-driven insights.',
                'responsibilities' => "- Prepare monthly/quarterly financial reports\n"
                                   ."- Build financial models and scenario analyses\n"
                                   ."- Track KPIs and present to the leadership team\n"
                                   ."- Support budgeting and forecasting processes\n"
                                   .'- Conduct variance analysis against forecasts',
                'required_skills' => 'Excel, Financial Modeling, Power BI, SQL, Tally, GST',
                'location' => 'Mumbai, Maharashtra',
                'min_salary' => 900000,
                'max_salary' => 1200000,
                'type' => 'Full-time',
                'experience' => '3+ Years',
                'last_date' => now()->addDays(15)->format('Y-m-d'),
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'admin_id' => $financehub,
                'category_id' => $finance,
                'role_id' => $accRole,
                'title' => 'Junior Accountant',
                'description' => 'Handle day-to-day accounting operations, GST filing, '
                                   .'bank reconciliation, and vendor payments.',
                'overview' => 'We are looking for a detail-oriented Junior Accountant '
                                   .'to join our finance team in Mumbai. This is an excellent '
                                   .'opportunity to grow in a fast-paced FinTech environment.',
                'responsibilities' => "- Maintain books of accounts in Tally/SAP\n"
                                   ."- File monthly GST returns (GSTR-1 and GSTR-3B)\n"
                                   ."- Perform bank reconciliation statements\n"
                                   ."- Process vendor invoices and employee expense claims\n"
                                   .'- Assist in TDS computation and filing',
                'required_skills' => 'Tally ERP, GST, TDS, Bank Reconciliation, MS Excel',
                'location' => 'Mumbai, Maharashtra',
                'min_salary' => 350000,
                'max_salary' => 500000,
                'type' => 'Full-time',
                'experience' => '1 Year',
                'last_date' => now()->addDays(28)->format('Y-m-d'),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
            [
                'admin_id' => $financehub,
                'category_id' => $finance,
                'role_id' => $faRole,
                'title' => 'Finance Intern (Part-time)',
                'description' => 'A 6-month paid internship for MBA Finance students. '
                                   .'Work alongside senior analysts on live projects.',
                'overview' => 'FinanceHub runs a summer internship program every year. '
                                   .'Selected interns work on real financial models and '
                                   .'get a PPO (Pre-Placement Offer) based on performance.',
                'responsibilities' => "- Assist in financial data collection and analysis\n"
                                   ."- Build dashboards using Excel/Power BI\n"
                                   ."- Support senior analysts in report preparation\n"
                                   .'- Attend client meetings and take notes',
                'required_skills' => 'MS Excel, Basic Finance, Power BI (preferred)',
                'location' => 'Mumbai, Maharashtra',
                'min_salary' => 150000,
                'max_salary' => 200000,
                'type' => 'Internship',
                'experience' => 'Fresher',
                'last_date' => now()->addDays(10)->format('Y-m-d'),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],

            // ── DesignStudio Jobs ────────────────────────────
            [
                'admin_id' => $designstudio,
                'category_id' => $design,
                'role_id' => $uxRole,
                'title' => 'UI/UX Designer',
                'description' => 'Design beautiful and intuitive user experiences for '
                                   .'web and mobile applications using Figma.',
                'overview' => 'DesignStudio is hiring a UI/UX Designer to work on '
                                   .'projects for top brands in the FMCG, FinTech, and '
                                   .'E-commerce sectors. You will own projects end-to-end.',
                'responsibilities' => "- Create wireframes, user flows, and high-fidelity mockups\n"
                                   ."- Conduct user research and usability testing\n"
                                   ."- Collaborate with developers for design handoff\n"
                                   ."- Maintain and evolve the design system\n"
                                   .'- Present designs to clients and incorporate feedback',
                'required_skills' => 'Figma, Adobe XD, User Research, Prototyping, Design Systems',
                'location' => 'Pune, Maharashtra',
                'min_salary' => 600000,
                'max_salary' => 800000,
                'type' => 'Full-time',
                'experience' => '2 Years',
                'last_date' => now()->addDays(20)->format('Y-m-d'),
                'created_at' => now()->subDays(9),
                'updated_at' => now()->subDays(9),
            ],
            [
                'admin_id' => $designstudio,
                'category_id' => $design,
                'role_id' => $gdRole,
                'title' => 'Graphic Designer',
                'description' => 'Create stunning visual content for social media, '
                                   .'branding, and marketing campaigns.',
                'overview' => 'We are looking for a creative Graphic Designer who '
                                   .'can translate brand guidelines into compelling visual '
                                   .'content across digital and print channels.',
                'responsibilities' => "- Design social media posts, banners, and ads\n"
                                   ."- Create brand identity materials (logos, letterheads)\n"
                                   ."- Design print collaterals (brochures, packaging)\n"
                                   ."- Edit product photos and create marketing videos\n"
                                   .'- Collaborate with the marketing team on campaigns',
                'required_skills' => 'Adobe Photoshop, Illustrator, Canva, After Effects, Typography',
                'location' => 'Pune, Maharashtra',
                'min_salary' => 400000,
                'max_salary' => 600000,
                'type' => 'Full-time',
                'experience' => '1 Year',
                'last_date' => now()->addDays(35)->format('Y-m-d'),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'admin_id' => $designstudio,
                'category_id' => $mktg,
                'role_id' => $dmRole,
                'title' => 'Digital Marketing Executive',
                'description' => 'Plan and execute digital marketing campaigns across '
                                   .'Google, Meta, and LinkedIn to drive business growth.',
                'overview' => 'DesignStudio is expanding its in-house marketing team. '
                                   .'You will manage ad budgets, track performance, and '
                                   .'deliver ROI-positive campaigns for our agency clients.',
                'responsibilities' => "- Plan and run Google Ads and Meta Ads campaigns\n"
                                   ."- Perform keyword research and on-page SEO\n"
                                   ."- Write compelling ad copy and landing page content\n"
                                   ."- Track campaign performance using Google Analytics\n"
                                   .'- Prepare weekly performance reports for clients',
                'required_skills' => 'Google Ads, Meta Ads, SEO, Google Analytics, Content Writing',
                'location' => 'Remote (India)',
                'min_salary' => 450000,
                'max_salary' => 650000,
                'type' => 'Full-time',
                'experience' => '1 Year',
                'last_date' => now()->addDays(25)->format('Y-m-d'),
                'created_at' => now()->subDays(11),
                'updated_at' => now()->subDays(11),
            ],
            [
                'admin_id' => $techcorp,
                'category_id' => $it,
                'role_id' => $phpRole,
                'title' => 'Laravel Developer (Contract)',
                'description' => '6-month contract role for an experienced Laravel developer '
                                   .'to work on a client migration project.',
                'overview' => 'TechCorp needs a contract Laravel developer to migrate '
                                   .'a legacy CodeIgniter application to Laravel 11. '
                                   .'The project is well-defined with clear deliverables.',
                'responsibilities' => "- Migrate CodeIgniter modules to Laravel 11\n"
                                   ."- Write test coverage for migrated modules\n"
                                   ."- Document all API endpoints using Postman\n"
                                   ."- Coordinate with the client's QA team",
                'required_skills' => 'Laravel, PHP, MySQL, REST API, PHPUnit, Git',
                'location' => 'Remote (India)',
                'min_salary' => 750000,
                'max_salary' => 950000,
                'type' => 'Contract',
                'experience' => '2 Years',
                'last_date' => now()->addDays(7)->format('Y-m-d'),
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(12),
            ],
        ];

        DB::table('jobs')->insert($jobs);
        $this->command->info('✅ Jobs seeded: 12 jobs across 3 companies');
    }
}
