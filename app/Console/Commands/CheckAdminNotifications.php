<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\PendingReminderNotification;
use App\Notifications\JobExpiryNotification;
use App\Notifications\ProfileIncompleteNotification;

class CheckAdminNotifications extends Command
{
    protected $signature = 'admin:check-notifications';
    protected $description = 'Check and send admin notifications';

    public function handle()
    {
        $admins = Admin::all();

        foreach ($admins as $admin) {

            $jobIds = Job::where('admin_id', $admin->id)->pluck('id');

            // 1️⃣ Pending Applications
            $pendingCount = JobApplication::whereIn('job_id', $jobIds)
                ->where('status', 'pending')
                ->count();

            if ($pendingCount > 0) {
                $admin->notify(new PendingReminderNotification($pendingCount));
            }

            // 2️⃣ Expiring Jobs
            $expiringJobs = Job::where('admin_id', $admin->id)
                ->whereBetween('last_date', [now(), now()->addDays(2)])
                ->get();

            foreach ($expiringJobs as $job) {
                $admin->notify(new JobExpiryNotification($job));
            }

            // 3️⃣ Profile Incomplete
            if (empty($admin->expertise) || empty($admin->location)) {
                $admin->notify(new ProfileIncompleteNotification());
            }
        }

        $this->info('Admin notifications checked successfully.');
    }
}