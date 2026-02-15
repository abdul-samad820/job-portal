<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
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
        ];
    }
}