<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationConfirmation extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Delight Afriq')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your Delight Afriq account has been created.')
            ->line('African Heritage. Modern Luxury.')
            ->action('Continue shopping', url('/'));
    }
}
