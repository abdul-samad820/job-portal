<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class JobInviteNotification extends Notification
{
    protected $invite;

    public function __construct($invite)
    {
        $this->invite = $invite;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $companyName = $this->invite->job->admin->company_name ?? 'A company';

        return [
            'job_id' => $this->invite->job_id,
            'invite_id' => $this->invite->id,
            'message' => "{$companyName} invited you to apply for \"{$this->invite->job->title}\"",
            'url' => route('apply_form_job_application', ['id' => $this->invite->job_id]),
        ];
    }
}
