<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('faqs')->delete();

        $faqs = [
            [
                'question' => 'How do I create an account on JobHub?',
                'answer' => 'Click the "Register" button on the top navigation bar. '
                              .'Fill in your name, email, and password. A verification email '
                              .'will be sent to your inbox — click the link to activate your account. '
                              .'You can then complete your profile to start applying for jobs.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do I apply for a job?',
                'answer' => 'Browse jobs on the Jobs page and click on any listing that interests you. '
                              .'On the job detail page, click "Apply Now". You will be asked to upload '
                              .'your resume (PDF, max 2MB) and write a cover letter. '
                              .'Make sure your profile is complete before applying.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I withdraw my application after applying?',
                'answer' => 'Yes, you can withdraw a "Pending" application from your Applied Jobs page. '
                              .'Click the "Withdraw" button next to the application. '
                              .'Note: You cannot withdraw applications that have already been '
                              .'shortlisted, rejected, or where you have been hired.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How do Job Alerts work?',
                'answer' => 'Go to Job Alerts in your dashboard and enter keywords like "Laravel, PHP, Remote". '
                              .'Our system will scan new job postings daily and send you an email '
                              .'every morning at 9 AM with all matching jobs. '
                              .'You can pause or delete your alert anytime.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How can companies post jobs on JobHub?',
                'answer' => 'Companies need to register as an Admin account. '
                              .'The SuperAdmin reviews and approves company registrations. '
                              .'Once approved, companies can post unlimited jobs, '
                              .'manage applications, schedule interviews, and track hiring progress '
                              .'through their dedicated admin dashboard.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Is my personal data safe on JobHub?',
                'answer' => 'Yes, we take data security seriously. Your password is encrypted '
                              .'using bcrypt hashing and never stored in plain text. '
                              .'Resume files are stored securely and only accessible to the '
                              .'company you applied to. We do not share your data with third parties.',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('faqs')->insert($faqs);
        $this->command->info(' FAQs seeded: 6 FAQs');
    }
}
