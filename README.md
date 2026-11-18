# Delight Afriq

Laravel 12 e-commerce platform for Delight Afriq. The application replaces the former static marketing site with an authenticated, role-based Laravel monolith while preserving the original photography and video assets.

Phase 5 is complete: foundation, branded storefront shell, authentication, role/permission system, product catalogue, cart, checkout, orders, saved addresses, admin order management, Paystack payments with signed webhooks, payment status automation, sales reporting, admin-managed content, newsletter, SEO, and deployment checks.

## Stack

- PHP 8.2+
- Laravel 12
- Livewire 3
- Tailwind CSS 4 with Vite 7
- MySQL for local/production data
- SQLite in-memory for tests
- Spatie Laravel Permission 6
- PHPUnit 11
- Laravel Pint

## Brand

- Name: Delight Afriq
- Tagline: African Heritage. Modern Luxury.
- Location: Silverbird Gallery, Abuja, Nigeria
- WhatsApp: `+2348030613742`
- Instagram: `https://www.instagram.com/delight_afriq/`
- Brand colors: navy `#0C1A3A`, gold `#C9A227`, cream `#F8F1E4`

Original static pages are archived in `archive/static-site/`. Original media is served from `public/media/images/` and `public/media/videos/`.

## Requirements

- PHP 8.2+ with `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, and `fileinfo`
- Composer 2
- Node 20+ and npm
- MySQL 8+ or MariaDB
- Optional but recommended for later phases: `bcmath`, `intl`, `gd`

## Local setup

1. Install dependencies:

   ```bash
   composer install
   npm install
   ```

2. Create the environment file and application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Create a MySQL database and update `DB_*` values in `.env`.

   Example local database:

   ```sql
   CREATE DATABASE IF NOT EXISTS delight_afriq
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci;
   ```

4. For local seeding, set real local values for:

   ```dotenv
   SUPER_ADMIN_NAME="Delight Afriq Administrator"
   SUPER_ADMIN_EMAIL="admin@delightafriq.test"
   SUPER_ADMIN_PASSWORD="change-this-password"
   ```

   Never commit real credentials. `.env` is gitignored.

5. Migrate and seed:

   ```bash
   php artisan migrate --seed
   ```

     This creates the users, cache, jobs, activity logs, Spatie permission, catalogue, cart, address, order, order item, payment transaction, content page, banner, homepage section, gallery item, and newsletter subscriber tables. It also seeds 8 roles, 48 permissions, the super administrator defined in `.env`, a demo catalogue, and seeded content using the preserved media in `public/media/images/`.

6. Link public uploads:

   ```bash
   php artisan storage:link
   ```

7. Build assets:

   ```bash
   npm run build
   ```

8. Serve the application:

   ```bash
   php artisan serve
   ```

   The app is then available at `http://127.0.0.1:8000` unless `APP_URL` is changed.

## Development workflow

Run the full local stack with:

```bash
composer dev
```

This starts the web server, queue worker, Pail log viewer, and Vite dev server.

## Tests and code style

Run tests:

```bash
php artisan test
# or
composer test
```

Tests run against SQLite in memory using `phpunit.xml`; they do not touch the local MySQL database.

Check formatting:

```bash
./vendor/bin/pint --test
```

Apply formatting:

```bash
./vendor/bin/pint
```

Current verification: 90 tests, 344 assertions, Pint pass, production asset build pass, catalogue/cart/checkout/payment/report/content/newsletter/SEO route smoke pass.

## Access control

The application uses one `web` guard with role-scoped areas:

- `/login` for customers
- `/admin/login` for staff
- `/account` for verified customers
- `/admin` for active staff

Roles are defined in `app/Support/Access/Roles.php`:

- Super Administrator
- Administrator
- Store Manager
- Sales Manager
- Order Processing Staff
- Accountant
- Content Manager
- Customer Support

Permissions are defined in `app/Support/Access/Permissions.php` and synchronized by `RoleCatalog::sync()`. `Super Administrator` is protected from deletion/modification by `RolePolicy` and service-layer checks.

## Catalogue

Storefront routes:

- `/collections` — category/collection filtered product catalogue with search and sort.
- `/products/{product:slug}` — product detail page with images, availability, and related products.

Admin catalogue routes are permission-gated and policy-protected:

- `/admin/categories`
- `/admin/collections`
- `/admin/products`
- `/admin/inventory`

Business logic lives in `app/Services/Catalogue/`, validation in `app/Http/Requests/Admin/`, and product media URLs resolve through `App\Support\Media\MediaUrl`. Uploaded product images are stored on the `public` disk under `products/` and require `php artisan storage:link`.

## Commerce

Storefront routes:

- `/cart` — guest or customer shopping bag with stock-aware quantity controls.
- `/checkout` — verified-customer checkout with saved or new delivery address.
- `/checkout/{order}/payment` — WhatsApp handoff or Paystack payment status/retry page.

Customer routes:

- `/account/orders` — order history.
- `/account/orders/{order}` — order detail and cancellation for pending orders.
- `/account/addresses` — saved delivery addresses.

Admin routes are permission-gated and policy-protected:

- `/admin/orders`
- `/admin/orders/{order}`

Cart and order logic lives in `app/Services/Commerce/`. Currency calculations use integer minor units through `App\Support\Commerce\Money`; shipping and order status rules live in `App\Support\Commerce\` and `App\Support\Orders\`. Placing an order reserves inventory, and cancelling releases that reservation.

## Payments and reports

Paystack routes:

- `POST /api/webhooks/paystack` — signed Paystack webhook endpoint.
- `GET /checkout/paystack/callback` — verifies the redirected transaction and confirms payment.
- `POST /checkout/{order}/paystack/retry` — starts a fresh Paystack transaction when needed.

Admin report route:

- `/admin/reports` — paid revenue, order counts, daily sales, and top products.

Payments are initialized and verified server-side in `App\Services\Payments\PaystackPaymentService` using `App\Support\Payments\PaystackClient`. Webhooks accept both the timestamped `Paystack-Signature` header and the legacy `X-Paystack-Signature` header, and payment confirmation is idempotent. Successful payments mark the order as paid, set `paid_at`, move pending orders to `processing`, record audit entries, and notify the customer. Payment attempts are stored in `payment_transactions`.

Set `PAYSTACK_SECRET_KEY`, `PAYSTACK_WEBHOOK_SECRET`, and `PAYMENT_PAYSTACK_ENABLED=true` in `.env`, then register `https://your-domain.example/api/webhooks/paystack` in the Paystack dashboard.

## Content, newsletter and SEO

Storefront routes:

- `/pages/{page:slug}` — published content pages such as About and Contact.
- `/gallery` — admin-managed image gallery.
- `/sitemap.xml` — XML sitemap for public products, pages and core routes.
- `/robots.txt` — dynamic robots policy with a sitemap reference.

Admin routes are permission-gated and policy-protected:

- `/admin/pages`
- `/admin/homepage-sections`
- `/admin/banners`
- `/admin/gallery`
- `/admin/newsletter`

Newsletter signup uses double opt-in. Verification and unsubscribe links are signed and time-limited; successful verification sends a welcome email. The admin CSV export prefixes spreadsheet-triggering characters with an apostrophe to reduce CSV injection risk.

`public/robots.txt` has been removed so the dynamic `/robots.txt` route can respond. Storefront pages emit per-page titles, descriptions, canonical URLs, Open Graph and Twitter card metadata with a logo fallback image.

## Deployment

Before deploying, set `APP_ENV=production`, `APP_DEBUG=false`, an HTTPS `APP_URL`, `FORCE_HTTPS=true`, `SESSION_SECURE_COOKIE=true`, and a secure `APP_KEY`. Set `TRUSTED_PROXIES` when HTTPS is terminated by a reverse proxy or load balancer; comma-separated IPs or `*` are accepted.

Run the deployment validator after installing dependencies and migrating:

```bash
php artisan app:check-production
```

The command checks production environment values, secure cookies, trusted proxy configuration, storage link, writable directories, dynamic robots routing, Paystack credentials when payments are enabled, and pending migrations. Queue worker supervision and database backups remain deployment-platform responsibilities.

## Important implementation notes

- Money is stored as decimal values and calculated in integer minor units via `App\Support\Commerce\Money`; never use floating point for prices.
- Customer email verification is mandatory.
- Staff accounts are suspended by setting `is_active` to false and/or `suspended_at`; `EnsureAccountIsActive` signs out inactive users.
- Security headers are appended globally by `AddSecurityHeaders`.
- `Blade::anonymousComponentPath(resource_path('views'))` allows `x-layouts.*` components stored in `resources/views/layouts/`.
- `App\Http\Controllers\Controller` extends Laravel's base controller and uses `AuthorizesRequests` and `ValidatesRequests`, so `$this->authorize()` works in app controllers.
- Spatie's vendor `Role` model policy is registered explicitly in `AppServiceProvider` because Laravel policy auto-discovery only covers app models.
- Paystack integration is complete but disabled by default until real credentials and the webhook URL are configured.

## Roadmap

- Phase 1: Foundation, auth, RBAC, brand shell, tests — complete.
- Phase 2: Product catalogue, categories, collections, inventory, media management — complete.
- Phase 3: Cart, checkout, orders, customer account details, WhatsApp/Paystack handoff — complete.
- Phase 4: Paystack payments, webhooks, order status automation, reporting — complete.
- Phase 5: Content management, newsletter, SEO, deployment hardening — complete.

## License

Private project. All rights reserved.
