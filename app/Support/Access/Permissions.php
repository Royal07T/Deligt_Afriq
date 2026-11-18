<?php

namespace App\Support\Access;

final class Permissions
{
    public const PRODUCTS_VIEW = 'products.view';

    public const PRODUCTS_CREATE = 'products.create';

    public const PRODUCTS_EDIT = 'products.edit';

    public const PRODUCTS_DELETE = 'products.delete';

    public const CATEGORIES_VIEW = 'categories.view';

    public const CATEGORIES_CREATE = 'categories.create';

    public const CATEGORIES_EDIT = 'categories.edit';

    public const CATEGORIES_DELETE = 'categories.delete';

    public const COLLECTIONS_VIEW = 'collections.view';

    public const COLLECTIONS_CREATE = 'collections.create';

    public const COLLECTIONS_EDIT = 'collections.edit';

    public const COLLECTIONS_DELETE = 'collections.delete';

    public const INVENTORY_VIEW = 'inventory.view';

    public const INVENTORY_MANAGE = 'inventory.manage';

    public const ORDERS_VIEW = 'orders.view';

    public const ORDERS_CREATE = 'orders.create';

    public const ORDERS_UPDATE = 'orders.update';

    public const ORDERS_CANCEL = 'orders.cancel';

    public const PAYMENTS_VIEW = 'payments.view';

    public const PAYMENTS_REFUND = 'payments.refund';

    public const CUSTOMERS_VIEW = 'customers.view';

    public const CUSTOMERS_EDIT = 'customers.edit';

    public const REPORTS_VIEW = 'reports.view';

    public const STAFF_VIEW = 'staff.view';

    public const STAFF_MANAGE = 'staff.manage';

    public const ROLES_VIEW = 'roles.view';

    public const ROLES_MANAGE = 'roles.manage';

    public const SETTINGS_VIEW = 'settings.view';

    public const SETTINGS_MANAGE = 'settings.manage';

    public const CONTENT_VIEW = 'content.view';

    public const CONTENT_MANAGE = 'content.manage';

    public const BANNERS_VIEW = 'banners.view';

    public const BANNERS_MANAGE = 'banners.manage';

    public const HOMEPAGE_VIEW = 'homepage.view';

    public const HOMEPAGE_MANAGE = 'homepage.manage';

    public const GALLERY_VIEW = 'gallery.view';

    public const GALLERY_MANAGE = 'gallery.manage';

    public const NEWSLETTER_VIEW = 'newsletter.view';

    public const NEWSLETTER_MANAGE = 'newsletter.manage';

    public const DISCOUNTS_VIEW = 'discounts.view';

    public const DISCOUNTS_MANAGE = 'discounts.manage';

    public const REVIEWS_VIEW = 'reviews.view';

    public const REVIEWS_MANAGE = 'reviews.manage';

    public const BESPOKE_VIEW = 'bespoke.view';

    public const BESPOKE_MANAGE = 'bespoke.manage';

    public const SHIPMENTS_VIEW = 'shipments.view';

    public const SHIPMENTS_MANAGE = 'shipments.manage';

    public const ACTIVITY_LOGS_VIEW = 'activity_logs.view';

    public const ALL = [
        self::PRODUCTS_VIEW,
        self::PRODUCTS_CREATE,
        self::PRODUCTS_EDIT,
        self::PRODUCTS_DELETE,
        self::CATEGORIES_VIEW,
        self::CATEGORIES_CREATE,
        self::CATEGORIES_EDIT,
        self::CATEGORIES_DELETE,
        self::COLLECTIONS_VIEW,
        self::COLLECTIONS_CREATE,
        self::COLLECTIONS_EDIT,
        self::COLLECTIONS_DELETE,
        self::INVENTORY_VIEW,
        self::INVENTORY_MANAGE,
        self::ORDERS_VIEW,
        self::ORDERS_CREATE,
        self::ORDERS_UPDATE,
        self::ORDERS_CANCEL,
        self::PAYMENTS_VIEW,
        self::PAYMENTS_REFUND,
        self::CUSTOMERS_VIEW,
        self::CUSTOMERS_EDIT,
        self::REPORTS_VIEW,
        self::STAFF_VIEW,
        self::STAFF_MANAGE,
        self::ROLES_VIEW,
        self::ROLES_MANAGE,
        self::SETTINGS_VIEW,
        self::SETTINGS_MANAGE,
        self::CONTENT_VIEW,
        self::CONTENT_MANAGE,
        self::BANNERS_VIEW,
        self::BANNERS_MANAGE,
        self::HOMEPAGE_VIEW,
        self::HOMEPAGE_MANAGE,
        self::GALLERY_VIEW,
        self::GALLERY_MANAGE,
        self::NEWSLETTER_VIEW,
        self::NEWSLETTER_MANAGE,
        self::DISCOUNTS_VIEW,
        self::DISCOUNTS_MANAGE,
        self::REVIEWS_VIEW,
        self::REVIEWS_MANAGE,
        self::BESPOKE_VIEW,
        self::BESPOKE_MANAGE,
        self::SHIPMENTS_VIEW,
        self::SHIPMENTS_MANAGE,
        self::ACTIVITY_LOGS_VIEW,
    ];
}
