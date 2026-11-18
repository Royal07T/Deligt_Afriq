<?php

namespace App\Support\Orders;

final class PaymentStatus
{
    public const UNPAID = 'unpaid';

    public const PENDING = 'pending';

    public const PAID = 'paid';

    public const FAILED = 'failed';

    public const REFUNDED = 'refunded';

    public const ALL = [
        self::UNPAID,
        self::PENDING,
        self::PAID,
        self::FAILED,
        self::REFUNDED,
    ];
}
