<?php

namespace Tests;

use App\Support\Access\RoleCatalog;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends BaseTestCase
{
    protected function seedAccessControl(): void
    {
        RoleCatalog::sync();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
