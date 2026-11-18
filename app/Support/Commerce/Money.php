<?php

namespace App\Support\Commerce;

final class Money
{
    public static function toMinor(string|int|float $amount): int
    {
        $value = (string) $amount;
        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '+-');
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $fraction = substr($fraction.'00', 0, 2);
        $minor = ((int) $whole * 100) + (int) $fraction;

        return $negative ? -$minor : $minor;
    }

    public static function fromMinor(int $minor): string
    {
        $sign = $minor < 0 ? '-' : '';
        $minor = abs($minor);

        return sprintf('%s%d.%02d', $sign, intdiv($minor, 100), $minor % 100);
    }

    public static function format(int $minor, string $currency = 'NGN'): string
    {
        $amount = self::fromMinor($minor);
        [$whole, $fraction] = explode('.', $amount);

        return $currency.' '.number_format((int) $whole).'.'.$fraction;
    }
}
