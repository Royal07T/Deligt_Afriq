<?php

namespace App\Support\Commerce;

final class ShippingCalculator
{
    public static function calculate(string $subtotal): int
    {
        $threshold = Money::toMinor((string) config('store.free_shipping_threshold'));

        if (Money::toMinor($subtotal) >= $threshold) {
            return 0;
        }

        return Money::toMinor((string) config('store.shipping_flat_fee'));
    }
}
