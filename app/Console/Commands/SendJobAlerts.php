<?php

namespace App\Console\Commands;

use App\Mail\JobAlertMail;
use App\Models\Job;
use App\Models\JobAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendJobAlerts extends Command
{
    protected $signature = 'jobs:send-alerts';

    protected $description = 'Send matching job emails to users with active job alerts';

    public function handle(): void
    {
        $this->info('Job alerts process start...');

        // Load recent jobs ONCE — these are shared across all alert checks
        // Only select the columns we actually need
        $recentJobs = Job::with('admin')
            ->select(['id', 'title', 'description', 'required_skills', 'admin_id', 'last_date', 'type', 'location', 'min_salary', 'max_salary'])
            ->where('created_at', '>=', now()->subDay())
            ->whereDate('last_date', '>=', now())
            ->get();

        if ($recentJobs->isEmpty()) {
            $this->info('No new jobs posted in the last 24 hours.');

            return;
        }

        $this->info("Processing alerts against {$recentJobs->count()} recent jobs...");

        $emailsSent = 0;

        // chunk(200) — process 200 alerts at a time, not all at once
        // This keeps memory usage flat regardless of how many users you have
        JobAlert::with('user')
            ->where('is_active', true)
            ->whereNotNull('user_id')   // safety: skip orphaned alerts
            ->chunk(200, function ($alerts) use ($recentJobs, &$emailsSent) {

                foreach ($alerts as $alert) {

                    // Skip if user was deleted
                    if (! $alert->user) {
                        continue;
                    }

                    $keywords = $alert->keywords_array;

                    if (empty($keywords)) {
                        continue;
                    }

                    // Filter matching jobs in PHP (recentJobs already loaded)
                    $matchingJobs = $recentJobs->filter(function ($job) use ($keywords) {
                        $jobText = strtolower(
                            $job->title.' '.
                            $job->description.' '.
                            ($job->required_skills ?? '')
                        );

                        foreach ($keywords as $keyword) {
                            if (! empty($keyword) && str_contains($jobText, strtolower(trim($keyword)))) {
                                return true;
                            }
                        }

                        return false;
                    });

                    if ($matchingJobs->isNotEmpty()) {
                        // queue() — non-blocking, uses your database queue
                        Mail::to($alert->user->email)
                            ->queue(new JobAlertMail(
                                $alert->user,
                                $matchingJobs->values()->all(),
                                $alert->keywords
                            ));

                        $alert->update(['last_sent_at' => now()]);
                        $emailsSent++;

                        $this->info("  ✓ Alert sent: {$alert->user->name} ({$matchingJobs->count()} jobs)");
                    }
                }
            });

        $this->info("Complete! Total {$emailsSent} emails queued.");
    }
}
