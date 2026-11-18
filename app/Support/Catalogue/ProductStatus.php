<?php

namespace App\Support\Catalogue;

final class ProductStatus
{
    public const DRAFT = 'draft';

    public const ACTIVE = 'active';

    public const ARCHIVED = 'archived';

    public const ALL = [self::DRAFT, self::ACTIVE, self::ARCHIVED];
}
