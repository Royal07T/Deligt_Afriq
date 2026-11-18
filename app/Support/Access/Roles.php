<?php

namespace App\Support\Access;

final class Roles
{
    public const SUPER_ADMIN = 'Super Administrator';

    public const ADMINISTRATOR = 'Administrator';

    public const STORE_MANAGER = 'Store Manager';

    public const SALES_MANAGER = 'Sales Manager';

    public const ORDER_PROCESSING = 'Order Processing Staff';

    public const ACCOUNTANT = 'Accountant';

    public const CONTENT_MANAGER = 'Content Manager';

    public const CUSTOMER_SUPPORT = 'Customer Support';

    public const ALL = [
        self::SUPER_ADMIN,
        self::ADMINISTRATOR,
        self::STORE_MANAGER,
        self::SALES_MANAGER,
        self::ORDER_PROCESSING,
        self::ACCOUNTANT,
        self::CONTENT_MANAGER,
        self::CUSTOMER_SUPPORT,
    ];

    public const PROTECTED = [self::SUPER_ADMIN];
}
