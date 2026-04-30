<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

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
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'job_title' => $this->job->title,
            'status' => $this->status,
            'message' => "Your application for {$this->job->title} is {$this->status}",
        ];
    }
}
