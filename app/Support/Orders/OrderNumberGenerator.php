<?php

namespace App\Support\Orders;

use App\Models\Order;
use Illuminate\Support\Str;

final class OrderNumberGenerator
{
    public static function generate(): string
    {
        do {
            $number = 'DA-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
