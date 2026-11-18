<?php

namespace App\Support\Orders;

final class PaymentMethod
{
    public const WHATSAPP = 'whatsapp';

    public const PAYSTACK = 'paystack';

    public const LABELS = [
        self::WHATSAPP => 'WhatsApp concierge',
        self::PAYSTACK => 'Paystack',
    ];

    public static function available(): array
    {
        $methods = [self::WHATSAPP];

        if (config('store.payment_methods.paystack') && self::paystackConfigured()) {
            $methods[] = self::PAYSTACK;
        }

        return $methods;
    }

    private static function paystackConfigured(): bool
    {
        $secret = (string) config('services.paystack.secret_key', '');

        return $secret !== '' && ! str_contains($secret, 'replace-with');
    }
}
