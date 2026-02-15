<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewJobApplicationNotification extends Notification
{
    protected $job;
    protected $user;

    public function __construct($job, $user)
    {
        $this->job = $job;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Job Application',
            'message' => "New application received for {$this->job->title}",
            'job_id' => $this->job->id,
            'user_name' => $this->user->name,
        ];
    }
}