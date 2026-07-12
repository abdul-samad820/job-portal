<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    protected ContactMessage $contactMessage;

    public function __construct(ContactMessage $contactMessage)
    {
        $this->contactMessage = $contactMessage;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Contact Message',
            'message' => "{$this->contactMessage->name}: {$this->contactMessage->subject}",
            'url' => route('superadmin.contact.index'),
        ];
    }
}
