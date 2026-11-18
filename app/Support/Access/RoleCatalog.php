<?php

namespace App\Support\Access;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RoleCatalog
{
    public const ROLE_PERMISSIONS = [
        Roles::SUPER_ADMIN => Permissions::ALL,
        Roles::ADMINISTRATOR => [
            Permissions::PRODUCTS_VIEW,
            Permissions::PRODUCTS_CREATE,
            Permissions::PRODUCTS_EDIT,
            Permissions::PRODUCTS_DELETE,
            Permissions::CATEGORIES_VIEW,
            Permissions::CATEGORIES_CREATE,
            Permissions::CATEGORIES_EDIT,
            Permissions::CATEGORIES_DELETE,
            Permissions::COLLECTIONS_VIEW,
            Permissions::COLLECTIONS_CREATE,
            Permissions::COLLECTIONS_EDIT,
            Permissions::COLLECTIONS_DELETE,
            Permissions::INVENTORY_VIEW,
            Permissions::INVENTORY_MANAGE,
            Permissions::ORDERS_VIEW,
            Permissions::ORDERS_CREATE,
            Permissions::ORDERS_UPDATE,
            Permissions::ORDERS_CANCEL,
            Permissions::PAYMENTS_VIEW,
            Permissions::PAYMENTS_REFUND,
            Permissions::CUSTOMERS_VIEW,
            Permissions::CUSTOMERS_EDIT,
            Permissions::REPORTS_VIEW,
            Permissions::STAFF_VIEW,
            Permissions::ROLES_VIEW,
            Permissions::SETTINGS_VIEW,
            Permissions::CONTENT_VIEW,
            Permissions::CONTENT_MANAGE,
            Permissions::BANNERS_VIEW,
            Permissions::BANNERS_MANAGE,
            Permissions::HOMEPAGE_VIEW,
            Permissions::HOMEPAGE_MANAGE,
            Permissions::GALLERY_VIEW,
            Permissions::GALLERY_MANAGE,
            Permissions::NEWSLETTER_VIEW,
            Permissions::NEWSLETTER_MANAGE,
            Permissions::DISCOUNTS_VIEW,
            Permissions::DISCOUNTS_MANAGE,
            Permissions::REVIEWS_VIEW,
            Permissions::REVIEWS_MANAGE,
            Permissions::BESPOKE_VIEW,
            Permissions::BESPOKE_MANAGE,
            Permissions::SHIPMENTS_VIEW,
            Permissions::SHIPMENTS_MANAGE,
        ],
        Roles::STORE_MANAGER => [
            Permissions::PRODUCTS_VIEW,
            Permissions::PRODUCTS_CREATE,
            Permissions::PRODUCTS_EDIT,
            Permissions::CATEGORIES_VIEW,
            Permissions::CATEGORIES_CREATE,
            Permissions::CATEGORIES_EDIT,
            Permissions::COLLECTIONS_VIEW,
            Permissions::COLLECTIONS_CREATE,
            Permissions::COLLECTIONS_EDIT,
            Permissions::INVENTORY_VIEW,
            Permissions::INVENTORY_MANAGE,
            Permissions::ORDERS_VIEW,
            Permissions::ORDERS_UPDATE,
            Permissions::CUSTOMERS_VIEW,
            Permissions::REVIEWS_VIEW,
        ],
        Roles::SALES_MANAGER => [
            Permissions::PRODUCTS_VIEW,
            Permissions::ORDERS_VIEW,
            Permissions::ORDERS_UPDATE,
            Permissions::CUSTOMERS_VIEW,
            Permissions::CUSTOMERS_EDIT,
            Permissions::PAYMENTS_VIEW,
            Permissions::DISCOUNTS_VIEW,
            Permissions::DISCOUNTS_MANAGE,
            Permissions::REPORTS_VIEW,
        ],
        Roles::ORDER_PROCESSING => [
            Permissions::ORDERS_VIEW,
            Permissions::ORDERS_UPDATE,
            Permissions::INVENTORY_VIEW,
            Permissions::CUSTOMERS_VIEW,
            Permissions::SHIPMENTS_VIEW,
            Permissions::SHIPMENTS_MANAGE,
        ],
        Roles::ACCOUNTANT => [
            Permissions::ORDERS_VIEW,
            Permissions::PAYMENTS_VIEW,
            Permissions::PAYMENTS_REFUND,
            Permissions::DISCOUNTS_VIEW,
            Permissions::REPORTS_VIEW,
        ],
        Roles::CONTENT_MANAGER => [
            Permissions::PRODUCTS_VIEW,
            Permissions::PRODUCTS_EDIT,
            Permissions::CATEGORIES_VIEW,
            Permissions::COLLECTIONS_VIEW,
            Permissions::COLLECTIONS_EDIT,
            Permissions::CONTENT_VIEW,
            Permissions::CONTENT_MANAGE,
            Permissions::BANNERS_VIEW,
            Permissions::BANNERS_MANAGE,
            Permissions::HOMEPAGE_VIEW,
            Permissions::HOMEPAGE_MANAGE,
            Permissions::GALLERY_VIEW,
            Permissions::GALLERY_MANAGE,
            Permissions::NEWSLETTER_VIEW,
            Permissions::NEWSLETTER_MANAGE,
            Permissions::REVIEWS_VIEW,
            Permissions::REVIEWS_MANAGE,
        ],
        Roles::CUSTOMER_SUPPORT => [
            Permissions::ORDERS_VIEW,
            Permissions::CUSTOMERS_VIEW,
            Permissions::CUSTOMERS_EDIT,
            Permissions::BESPOKE_VIEW,
            Permissions::BESPOKE_MANAGE,
            Permissions::REVIEWS_VIEW,
            Permissions::NEWSLETTER_VIEW,
        ],
    ];

    public static function sync(): void
    {
        foreach (Permissions::ALL as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        app('cache')->forget(config('permission.cache.key'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }

        app('cache')->forget(config('permission.cache.key'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
