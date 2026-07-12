<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewJobFromFollowedCompanyNotification extends Notification
{
    protected $job;

    public function __construct($job)
    {
        $this->job = $job;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $companyName = $this->job->admin->company_name ?? 'A company you follow';

        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => "{$companyName} just posted a new job: {$this->job->title}",
            'url' => route('user.job_single', $this->job->id),
        ];
    }
}
