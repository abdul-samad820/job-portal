<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ProfileIncompleteNotification extends Notification
{
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Profile Incomplete',
            'message' => 'Complete your company profile to increase visibility.',
        ];
    }
}
