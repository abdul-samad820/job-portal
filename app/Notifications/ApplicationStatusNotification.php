<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationStatusNotification extends Notification
{
    protected $job;
    protected $status;

    public function __construct($job, $status)
    {
        $this->job = $job;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database']; // future me mail bhi add kar sakte ho
    }

    public function toDatabase($notifiable)
    {
        return [
            'job_title' => $this->job->title,
            'status' => $this->status,
            'message' => "Your application for {$this->job->title} is {$this->status}"
        ];
    }
}