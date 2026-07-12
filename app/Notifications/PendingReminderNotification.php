<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PendingReminderNotification extends Notification
{
    protected $count;

    public function __construct($count)
    {
        $this->count = $count;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Pending Applications',
            'message' => "{$this->count} applications are pending review.",
            'url' => route('job_application'),
        ];
    }
}
