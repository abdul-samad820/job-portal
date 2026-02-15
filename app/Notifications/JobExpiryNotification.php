<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
class JobExpiryNotification extends Notification
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
        return [
            'title' => 'Job Expiry Alert',
            'message' => "Your job post '{$this->job->title}' expires in 2 days.",
            'job_id' => $this->job->id,
        ];
    }
}