<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewsletterWelcomeNotification extends Notification
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
            'newsletter.unsubscribe',
            now()->addDays(30),
            ['token' => $this->subscriber->token],
        );

        return (new MailMessage)
            ->subject('Welcome to the Delight Afriq newsletter')
            ->greeting('Hello '.($this->subscriber->name ?: 'there').',')
            ->line('Your subscription is confirmed. Thank you for joining the Delight Afriq community.')
            ->line(config('brand.tagline'))
            ->action('Unsubscribe', $url);
    }
}
