<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $job;
    public $status;

    public function __construct($user, $job, $status)
    {
        $this->user = $user;
        $this->job = $job;
        $this->status = $status;
    }

    public function build()
    {
        return $this->subject('Application Status Update')
                    ->view('emails.application_status');
    }
}