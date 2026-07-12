<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class JobHiddenNotification extends Notification
{
    protected $job;

    protected $reason;

    public function __construct($job, string $reason)
    {
        $this->job = $job;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Job Hidden by Moderation',
            'message' => "Your job post \"{$this->job->title}\" was hidden by an admin. Reason: {$this->reason}",
            'job_id' => $this->job->id,
            'url' => route('admin.job'),
        ];
    }
}
