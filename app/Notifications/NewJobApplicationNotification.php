<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewJobApplicationNotification extends Notification
{
    use Queueable;

    protected $job;
    protected $user;

    public function __construct($job, $user)
    {
        $this->job  = $job;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

   public function toDatabase($notifiable)
{
    return [
        'title'          => 'New Job Application',
        'message'        => "New application received from {$this->user->name} for {$this->job->title}",
        'job_id'         => $this->job->id,
        'application_id' => $this->job->applications()
                                ->where('user_id', $this->user->id)
                                ->latest()
                                ->value('id'),
        'user_id'        => $this->user->id,
        'job_title'      => $this->job->title,
        'applied_at'     => now()->format('d M Y, h:i A'),
    ];
}

}
