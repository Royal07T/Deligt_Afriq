<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
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
            ->subject('Order '.$this->order->order_number.' update')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('The status of your Delight Afriq order has changed.')
            ->line('Order number: '.$this->order->order_number)
            ->line('New status: '.str_replace('_', ' ', ucfirst($this->order->status)))
            ->action('View your order', route('customer.orders.show', $this->order));
    }
}
