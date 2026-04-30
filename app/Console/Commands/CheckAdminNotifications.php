<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\JobExpiryNotification;
use App\Notifications\PendingReminderNotification;
use App\Notifications\ProfileIncompleteNotification;
use Illuminate\Console\Command;

class CheckAdminNotifications extends Command
{
    protected $signature = 'admin:check-notifications';

    protected $description = 'Check and send admin notifications';

    public function handle()
    {
        $admins = Admin::all();

        foreach ($admins as $admin) {

            $jobIds = Job::where('admin_id', $admin->id)->pluck('id');

            //Pending Applications
            $pendingCount = JobApplication::whereIn('job_id', $jobIds)
                ->where('status', 'pending')
                ->count();

            if ($pendingCount > 0) {
                $admin->notify(new PendingReminderNotification($pendingCount));
            }

            // Expiring Jobs
            $expiringJobs = Job::where('admin_id', $admin->id)
                ->whereBetween('last_date', [now(), now()->addDays(2)])
                ->get();

            foreach ($expiringJobs as $job) {
                $admin->notify(new JobExpiryNotification($job));
            }

            // Profile Incomplete
            if (empty($admin->expertise) || empty($admin->location)) {
                $admin->notify(new ProfileIncompleteNotification);
            }
        }

        $this->info('Admin notifications checked successfully.');
    }
}
