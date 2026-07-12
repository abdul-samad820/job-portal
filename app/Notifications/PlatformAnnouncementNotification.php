<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlatformAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $message,
        protected bool $sendEmail = false,
    ) {}

    public function via($notifiable): array
    {
        return $this->sendEmail ? ['database', 'mail'] : ['database'];
    }

    public function toDatabase($notifiable): array
    {
        // Previously hardcoded to '/' — clicking any broadcast announcement
        // yanked admins and users out of their panel straight to the public
        // homepage. Now sends each recipient back to their own dashboard
        // instead, based on which guard they belong to.
        $url = match (true) {
            $notifiable instanceof \App\Models\Admin => route('admin.dashboard'),
            $notifiable instanceof \App\Models\User => route('user.dashboard'),
            default => '/',
        };

        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $url,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->message)
            ->line('— The JobHub Team');
    }
}
