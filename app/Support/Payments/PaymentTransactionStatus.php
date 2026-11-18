<?php

namespace App\Support\Payments;

final class PaymentTransactionStatus
{
    public const PENDING = 'pending';

    public const SUCCESS = 'success';

    public const FAILED = 'failed';

    public const REFUNDED = 'refunded';

    public const ALL = [
        self::PENDING,
        self::SUCCESS,
        self::FAILED,
        self::REFUNDED,
    ];
}
