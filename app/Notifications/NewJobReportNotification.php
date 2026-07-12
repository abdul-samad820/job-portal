<?php

namespace App\Notifications;

use App\Models\JobReport;
use Illuminate\Notifications\Notification;

class NewJobReportNotification extends Notification
{
    protected JobReport $report;

    public function __construct(JobReport $report)
    {
        $this->report = $report;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $jobTitle = $this->report->job->title ?? 'a job listing';
        $reasonLabel = JobReport::REASONS[$this->report->reason] ?? ucfirst($this->report->reason);

        return [
            'title' => 'New Job Report',
            'message' => "\"{$jobTitle}\" was reported: {$reasonLabel}",
            'url' => route('superadmin.job-reports'),
            'job_id' => $this->report->job_id,
        ];
    }
}
