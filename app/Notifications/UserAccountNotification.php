<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAccountNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $subject,
        private readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->subject)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->message)
            ->action('Sign in', route('login'))
            ->line('For your security, your password is not included in this email.');
    }
}
