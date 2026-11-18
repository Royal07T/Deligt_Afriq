<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewsletterVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly NewsletterSubscriber $subscriber) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'newsletter.verify',
            now()->addDays(7),
            ['token' => $this->subscriber->token],
        );

        return (new MailMessage)
            ->subject('Confirm your Delight Afriq newsletter subscription')
            ->greeting('Hello '.($this->subscriber->name ?: 'there').',')
            ->line('Thanks for subscribing to the Delight Afriq newsletter.')
            ->line('Please confirm your email address to receive collection launches, styling notes and exclusive updates.')
            ->action('Confirm subscription', $url)
            ->line('This link expires in 7 days. If you did not subscribe, you can safely ignore this email.');
    }
}
