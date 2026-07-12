<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Interview $interview,
        public string $type = 'scheduled'
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $application = $this->interview->application;
        $job = $application->job;

        $subject = match ($this->type) {
            'scheduled' => "📅 Interview Scheduled — {$job->title}",
            'rescheduled' => "🔄 Interview Rescheduled — {$job->title}",
            'cancelled' => "❌ Interview Cancelled — {$job->title}",
            default => "Interview Update — {$job->title}",
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");

        // If cancelled
        if ($this->type === 'cancelled') {
            return $mail
                ->line("Your interview for the position **{$job->title}** has been cancelled.")
                ->line('If you have any questions, please contact the company directly.')
                ->salutation('Regards, '.config('app.name'));
        }

        return $mail
            ->line($this->type === 'rescheduled'
                ? 'Your interview has been successfully rescheduled.'
                : 'Congratulations! Your interview has been successfully scheduled.')
            ->line("**Position:** {$job->title}")
            ->line('**Company:** '.($job->admin->company_name ?? 'N/A'))
            ->line("**Date & Time:** {$this->interview->formatted_date_time}")
            ->line('**Mode:** '.ucfirst($this->interview->mode))
            ->line('**'.($this->interview->mode === 'online' ? 'Meeting Link' : 'Address').':** '.
                ($this->interview->location ?? 'Details will be shared soon'))
            ->when(
                $this->interview->notes,
                fn ($mail) => $mail->line("**Notes:** {$this->interview->notes}")
            )
            ->action('View Application', route('user.job_applied'))
            ->salutation('Best regards, '.config('app.name'));
    }

    public function toDatabase($notifiable): array
    {
        $job = $this->interview->application->job;

        return [
            'type' => 'interview_'.$this->type,
            'job_title' => $job->title,
            'date_time' => $this->interview->formatted_date_time,
            'mode' => $this->interview->mode,
            'location' => $this->interview->location,
            'status' => $this->type,
            'message' => match ($this->type) {
                'scheduled' => "Interview scheduled for {$job->title}",
                'rescheduled' => "Interview rescheduled for {$job->title}",
                'cancelled' => "Interview cancelled for {$job->title}",
                default => "Interview update for {$job->title}",
            },
            'url' => route('user.interviews'),
        ];
    }
}
