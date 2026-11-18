<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Delight Afriq order '.$this->order->order_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Thank you for your order. '.config('brand.tagline'))
            ->line('Order number: '.$this->order->order_number)
            ->line('Order total: '.$this->order->formattedTotal())
            ->line('We will contact you with the next steps shortly.')
            ->action('View your order', route('customer.orders.show', $this->order));
    }
}
