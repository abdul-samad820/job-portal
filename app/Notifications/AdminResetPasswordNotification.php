<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPasswordNotification extends Notification
{
    public $token;

    // constructor
    public function __construct($token)
    {
        $this->token = $token;
    }

    // channel
    public function via($notifiable)
    {
        return ['mail'];
    }

    // mail
    public function toMail($notifiable)
    {
        $url = url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ], false));

        return (new MailMessage)
            ->subject('Admin Password Reset')
            ->line('Click below to reset your password')
            ->action('Reset Password', $url)
            ->line('This link expires in 15 minutes.');
    }
}
