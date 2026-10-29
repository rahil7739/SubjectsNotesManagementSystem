<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationRequestNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly User $registeredUser) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New account awaiting approval',
            'message' => $this->registeredUser->name.' ('.$this->registeredUser->email.') requested an account.',
            'url' => route('admin.users.index').'#pending-approvals',
            'kind' => 'user-registration',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('A new StudySpace account needs approval')
            ->greeting('New registration request')
            ->line($this->registeredUser->name.' ('.$this->registeredUser->email.') has registered and is waiting for approval.')
            ->action('Review registration', route('admin.users.index').'#pending-approvals');
    }
}
