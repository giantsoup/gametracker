<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordlessLoginNotification extends Notification
{
    protected string $loginUrl;

    public function __construct(string $loginUrl)
    {
        $this->loginUrl = $loginUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Login Link')
            ->line('Click the button below to log in to your account.')
            ->action('Log In', $this->loginUrl)
            ->line('This link will expire in 30 minutes.')
            ->line('If you did not request this login link, no action is required.');
    }
}
