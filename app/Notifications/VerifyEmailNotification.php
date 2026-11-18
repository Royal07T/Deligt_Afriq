<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify your Delight Afriq email address')
            ->greeting('Hello '.$notifiable->name)
            ->line('Thank you for joining Delight Afriq. Please verify your email address to complete your account setup.')
            ->action('Verify email address', $this->verificationUrl($notifiable))
            ->line('If you did not create this account, no further action is required.');
    }
}
