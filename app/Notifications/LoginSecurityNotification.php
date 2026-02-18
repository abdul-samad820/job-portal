<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoginSecurityNotification extends Notification
{
    use Queueable;

    protected $ip;
    protected $loginTime;
    protected $userAgent;

    public function __construct($ip, $userAgent = null)
    {
        $this->ip        = $ip;
        $this->loginTime = now()->format('d M Y, h:i A');
        $this->userAgent = $userAgent;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'       => 'Security Alert',
            'message'     => "Hello {$notifiable->name}, new login detected.",
            'ip_address'  => $this->ip,
            'login_time'  => $this->loginTime,
            'browser'     => $this->userAgent,
        ];
    }
}
