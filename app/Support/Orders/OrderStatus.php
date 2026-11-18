<?php

namespace App\Support\Orders;

final class OrderStatus
{
    public const PENDING = 'pending';

    public const AWAITING_PAYMENT = 'awaiting_payment';

    public const PROCESSING = 'processing';

    public const SHIPPED = 'shipped';

    public const DELIVERED = 'delivered';

    public const CANCELLED = 'cancelled';

    public const REFUNDED = 'refunded';

    public const ALL = [
        self::PENDING,
        self::AWAITING_PAYMENT,
        self::PROCESSING,
        self::SHIPPED,
        self::DELIVERED,
        self::CANCELLED,
        self::REFUNDED,
    ];

    public const TRANSITIONS = [
        self::PENDING => [self::AWAITING_PAYMENT, self::PROCESSING, self::CANCELLED],
        self::AWAITING_PAYMENT => [self::PROCESSING, self::CANCELLED],
        self::PROCESSING => [self::SHIPPED, self::CANCELLED],
        self::SHIPPED => [self::DELIVERED],
        self::DELIVERED => [self::REFUNDED],
        self::CANCELLED => [],
        self::REFUNDED => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }
}
