<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $senderName;

    public string $senderEmail;

    public string $contactSubject;

    public string $userMessage;

    public function __construct(string $name, string $email, string $subject, string $message)
    {
        $this->senderName = $name;
        $this->senderEmail = $email;
        $this->contactSubject = $subject;
        $this->userMessage = $message;
    }

    public function build(): static
    {
        return $this
            ->subject('Contact Form: '.$this->contactSubject)
            ->replyTo($this->senderEmail, $this->senderName)
            ->view('emails.contact');
    }
}
