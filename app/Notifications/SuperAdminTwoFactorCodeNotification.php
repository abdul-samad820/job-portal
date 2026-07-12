<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuperAdminTwoFactorCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your SuperAdmin Login Code')
            ->greeting('Hello,')
            ->line('Use this code to finish logging in to the SuperAdmin panel:')
            ->line("**{$this->code}**")
            ->line('This code expires in 10 minutes.')
            ->line("If you didn't try to log in, please change your password immediately.");
    }
}
