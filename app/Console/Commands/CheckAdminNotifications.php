<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Notifications\JobExpiryNotification;
use App\Notifications\PendingReminderNotification;
use App\Notifications\ProfileIncompleteNotification;
use App\Traits\AdminNotificationHelper;
use Illuminate\Console\Command;

class CheckAdminNotifications extends Command
{
    use AdminNotificationHelper;

    protected $signature = 'admin:check-notifications';

    protected $description = 'Check and send admin notifications';

    public function handle()
    {
        $now = now();

        // Single query: every admin with their expiring-soon jobs eager
        // loaded, plus a pending-applications count computed in SQL.
        // Previously: 2 extra queries PER ADMIN (Job::where + JobApplication
        // ::whereIn) — with 50 admins that was 100 queries; now it's 1.
        $admins = Admin::where('role', 'admin')
            ->with(['jobs' => function ($query) use ($now) {
                $query->whereBetween('last_date', [$now, $now->copy()->addDays(2)]);
            }])
            ->withCount(['jobApplications as pending_applications_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->get();

        foreach ($admins as $admin) {

            // Pending Applications
            if ($admin->pending_applications_count > 0 && ! $this->hasNotification($admin->id, PendingReminderNotification::class)) {
                $admin->notify(new PendingReminderNotification($admin->pending_applications_count));
            }

            // Expiring Jobs (already eager loaded — no extra query)
            foreach ($admin->jobs as $job) {
                if (! $this->hasNotification($admin->id, JobExpiryNotification::class, $job->id)) {
                    $admin->notify(new JobExpiryNotification($job));
                }
            }

            // Profile Incomplete
            if (
                (empty($admin->expertise) || empty($admin->location)) &&
                ! $this->hasNotification($admin->id, ProfileIncompleteNotification::class)
            ) {
                $admin->notify(new ProfileIncompleteNotification);
            }
        }

        $this->info('Admin notifications checked successfully.');
    }
}
