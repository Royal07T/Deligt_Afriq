<?php

namespace App\Support\Orders;

use App\Models\Order;

final class WhatsAppHandoff
{
    public static function link(Order $order): string
    {
        $message = 'Hello Delight Afriq, I would like to pay for order '.$order->order_number
            .' ('.$order->formattedTotal().').';

        return config('brand.whatsapp_url').'?text='.urlencode($message);
    }
}
