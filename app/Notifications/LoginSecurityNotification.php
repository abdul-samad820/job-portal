<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginSecurityNotification extends Notification
{
    protected $ip;

    public function __construct($ip)
    {
        $this->ip = $ip;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Security Alert',
            'message' => "New login detected from IP: {$this->ip}",
        ];
    }
}