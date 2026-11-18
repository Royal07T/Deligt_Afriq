<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
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
            ->subject('Payment received for order '.$this->order->order_number)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('We have received your payment and your order is now being prepared.')
            ->line('Order number: '.$this->order->order_number)
            ->line('Amount paid: '.$this->order->formattedTotal())
            ->action('View your order', route('customer.orders.show', $this->order));
    }
}
