# Development Progress

Project: Delight Afriq Laravel e-commerce platform
Date: 2026-11-18
Current phase: Phase 5 — content management, newsletter, SEO, and deployment hardening

## Status

Phase 1, Phase 2, Phase 3, Phase 4, and Phase 5 are complete and verified. The implementation plan is finished; remaining work is operational deployment and future enhancements.

## What was completed

### Repository and asset migration

- Inspected the original static site and environment.
- Moved original static pages into `archive/static-site/`.
- Moved all original images to `public/media/images/`.
- Moved all original videos to `public/media/videos/`.
- Removed the empty `delight e-commerce/` directory.
- Kept the original static README in `archive/static-site/README.md`.
- Replaced the root `README.md` with Laravel/Delight Afriq setup documentation.

### Laravel foundation

- Installed Laravel 12 into the repository root.
- Installed and configured:
  - `livewire/livewire` 3
  - `spatie/laravel-permission` 6
  - Tailwind CSS 4 and Vite 7
- Generated `public/build/manifest.json` with `npm run build`.
- Published Spatie permission config to `config/permission.php`.
- Published the permission migration to `database/migrations/2026_11_18_011810_create_permission_tables.php` because package auto-loading did not register it.
- Configured `.env` for local MySQL, mail placeholders, super admin, Paystack placeholders, and security flags.
- Configured `.env.example` with production-safe placeholders.

### Database and access control

- Created local MySQL database `delight_afriq`.
- Created local MySQL users `delight` at `%` and `localhost` to avoid anonymous-user access issues.
- Extended the users table with staff/customer fields.
- Added `activity_logs`, Spatie permission, catalogue, cart, address, and order tables.
- Seeded:
  - 8 roles
  - 48 permissions
  - 1 super administrator from `SUPER_ADMIN_*` env values
- Implemented `RoleCatalog::sync()` as idempotent and cache-safe.
- Added `StaffAccountService` protections:
  - Non-super-admins cannot assign the super admin role.
  - Staff cannot change their own roles.
  - Staff cannot suspend their own account.
  - Non-super-admins cannot manage super admin accounts.
- Added policies for `User`, `Role`, `ActivityLog`, catalogue models, `Order`, and `Address`.
- Registered Spatie's vendor `Role` model policy explicitly with `Gate::policy()` because Laravel policy auto-discovery does not cover vendor models.
- Phase 3 reused the existing `ORDERS_VIEW`, `ORDERS_UPDATE`, and `ORDERS_CANCEL` permissions; no new permissions were added.

### Authentication and user flows

- Customer registration with mandatory email verification.
- Customer login/logout.
- Staff login/logout scoped to `/admin/login`.
- Password reset request/reset flow.
- Account dashboard and profile/password update pages.
- Branded notification classes for verification, registration confirmation, password reset, order placement, and order status changes.
- All password fields use a reusable show/hide visibility toggle in the `form-input` component.
- Inactive/suspended users are logged out by `EnsureAccountIsActive`.

### Views and middleware

- Added branded layouts:
  - `resources/views/layouts/storefront.blade.php`
  - `resources/views/layouts/customer.blade.php`
  - `resources/views/layouts/admin.blade.php`
- Added storefront, auth, verification, customer, and admin dashboard views.
- Registered middleware aliases:
  - `active`
  - `staff`
  - `role`
  - `permission`
  - `role_or_permission`
- Added global security headers.
- Registered `resources/views/` as an anonymous Blade component path so `x-layouts.*` resolves correctly.
- Added route throttling to login, registration-related password flows, verification resend, and checkout placement.

### Phase 2 catalogue recap

- Added categories, collections, products, product images, inventory, and collection-product pivot tables.
- Added catalogue models, factories, services, policies, requests, controllers, seeders, and views.
- Added public `/collections` and `/products/{product:slug}` routes.
- Added permission-gated admin catalogue routes.
- Added search, category/collection filtering, sorting, slug generation, media URL resolution, and image uploads.
- Added `php artisan storage:link` to serve uploaded product images.

### Phase 3 schema and support

Added migrations:

- `database/migrations/2026_11_18_030000_create_carts_table.php`
- `database/migrations/2026_11_18_030001_create_cart_items_table.php`
- `database/migrations/2026_11_18_030002_create_addresses_table.php`
- `database/migrations/2026_11_18_030003_create_orders_table.php`
- `database/migrations/2026_11_18_030004_create_order_items_table.php`

Added models:

- `App\Models\Cart`, `CartItem`, `Address`, `Order`, `OrderItem`
- Added `carts()`, `addresses()`, and `orders()` relations to `App\Models\User`.
- Added `cartItems()` and `orderItems()` relations to `App\Models\Product`.

Added support classes:

- `App\Support\Commerce\Money` — integer minor-unit conversion and formatting.
- `App\Support\Commerce\ShippingCalculator` — flat fee with free-shipping threshold.
- `App\Support\Orders\OrderStatus` — statuses and allowed transitions.
- `App\Support\Orders\PaymentStatus`
- `App\Support\Orders\PaymentMethod`
- `App\Support\Orders\OrderNumberGenerator`
- `App\Support\Orders\WhatsAppHandoff`

Added `config/store.php` and `.env.example` flags for shipping fees, free-shipping threshold, WhatsApp handoff, and disabled Paystack checkout.

### Phase 3 services and policies

- Added `CartService`:
  - Guest carts use a session token.
  - Guest carts merge into the user cart on login.
  - Stock-aware add/update/remove.
  - Cart subtotal and item counts.
- Added `OrderService`:
  - Cart revalidation inside a DB transaction with row locks.
  - Price snapshots in order items.
  - Price, shipping, discount, and total calculation in minor units.
  - Inventory reservation on order placement.
  - Inventory release on cancellation/refund.
  - Validated admin status transitions.
  - Audit records for `order.placed`, `order.cancelled`, and `order.status_updated`.
- Added `AddressService` for saved-address CRUD and default-address handling.
- Added `OrderPolicy` for owner/admin access and cancellation rules.
- Added `AddressPolicy` for owner-only access.

### Phase 3 controllers, requests, and routes

- Added storefront cart routes:
  - `GET /cart`
  - `POST /cart`
  - `PATCH /cart/{product}`
  - `DELETE /cart/{product}`
- Added verified-customer checkout routes:
  - `GET /checkout`
  - `POST /checkout`
  - `GET /checkout/{order}/payment`
- Added customer account routes:
  - `/account/orders`
  - `/account/orders/{order}`
  - `POST /account/orders/{order}/cancel`
  - `/account/addresses` CRUD and default-address route
- Added admin order routes:
  - `/admin/orders`
  - `/admin/orders/{order}`
  - `PUT /admin/orders/{order}/status`
- Added requests:
  - `AddToCartRequest`, `UpdateCartItemRequest`, `CheckoutRequest`
  - `StoreAddressRequest`, `UpdateAddressRequest`, `UpdateOrderStatusRequest`

### Phase 3 views

- Added storefront cart, checkout, and payment handoff views.
- Added product-detail add-to-bag form while retaining the WhatsApp inquiry link.
- Added customer order history and order detail views.
- Added customer saved-address index/create/edit views and shared form partial.
- Added admin order index and detail views with status management.
- Updated storefront, customer, and admin navigation.
- Updated admin dashboard with order count.
- Updated customer dashboard quick links.
- Corrected registration copy to state that a verified account is required to check out.

### Notifications and factories

- Added `OrderPlacedNotification`, `OrderStatusChangedNotification`, and `PaymentReceivedNotification`.
- Added factories:
  - `CartFactory`, `CartItemFactory`, `AddressFactory`, `OrderFactory`, `OrderItemFactory`, `PaymentTransactionFactory`

### Phase 4 payments

- Added `config/services.php` Paystack settings and `.env.example` keys for base URL, public/secret keys, webhook secret, and mode.
- Added `App\Exceptions\PaystackException` and `App\Support\Payments\PaystackClient` using Laravel HTTP client.
- Implemented server-side transaction initialization and verification with amount, currency, and reference validation.
- Added signature verification for both the timestamped `Paystack-Signature` header with a 300-second tolerance and the legacy `X-Paystack-Signature` header.
- Added `payment_transactions` table and `App\Models\PaymentTransaction` to record provider, status, amount, payload, external ID, and paid timestamp.
- Added `App\Services\Payments\PaystackPaymentService`:
  - Starts Paystack checkout and redirects to `authorization_url`.
  - Supports retry with a fresh payment reference.
  - Handles callback verification safely.
  - Handles signed webhooks idempotently.
  - Marks orders paid, sets `paid_at`, moves pending orders to `processing`, records audit entries, and notifies customers.
  - Leaves already-paid orders untouched on duplicate webhooks/callbacks.
  - Records failed/abandoned payments without changing order status.
- Added `App\Http\Controllers\Webhooks\PaystackController` at `POST /api/webhooks/paystack` and registered `routes/api.php` in `bootstrap/app.php`.
- Added checkout callback and retry routes, and updated the payment page to show paid state, payment attempts, and retry action.
- Updated `PaymentMethod::LABELS` so Paystack is no longer marked coming soon.

### Phase 4 reporting

- Added `SalesReportService` with paid revenue, paid/pending/cancelled order counts, refunds, average order value, daily sales, and top products.
- Added `Admin\ReportController` and `/admin/reports` guarded by `reports.view`.
- Added the reports view with date filters and tables for daily sales and top products.
- Added Reports navigation and paid revenue to the admin dashboard.

### Phase 5 content management

- Added `content_pages`, `banners`, `homepage_sections`, and `gallery_items` tables with models, policies, requests, admin CRUD controllers and admin views.
- Seeded About and Contact pages from the archived static site, plus homepage banners, editable homepage sections and gallery items through `ContentSeeder`.
- Added `PageRenderer` to safely render escaped paragraphs and line breaks from admin-managed body copy.
- Added `MediaService` for banner/gallery image uploads on the `public` disk with replacement and deletion handling.
- Added admin navigation entries for Pages, Homepage, Banners, Gallery and Newsletter.
- Updated the storefront layout and home page to render banners, homepage sections, gallery links, published page links and newsletter signup.

### Phase 5 newsletter

- Added `newsletter_subscribers` table and `NewsletterSubscriber` model with pending, subscribed and unsubscribed states.
- Added double opt-in signup, signed verification links, welcome notification and signed unsubscribe links.
- Added an admin subscriber list with search/status filters, delete action and CSV export.
- Added `CsvSanitizer` to neutralize spreadsheet formulas in exported cells.

### Phase 5 SEO and deployment hardening

- Added `config/seo.php`, canonical URLs, Open Graph image/title/description, Twitter card tags and per-page metadata across storefront controllers and views.
- Added `SitemapGenerator` and dynamic `/sitemap.xml`.
- Replaced static `public/robots.txt` with a dynamic `/robots.txt` route that advertises the sitemap and blocks private areas.
- Added trusted proxy configuration from `TRUSTED_PROXIES`, explicitly allowing forwarded host/proto/port headers.
- Added `config/deployment.php` and `php artisan app:check-production` to validate production environment, HTTPS, secure cookies, trusted proxies, storage link, writable paths, robots routing, Paystack credentials and pending migrations.
- Added `TRUSTED_PROXIES` to `.env.example` and documented deployment validation in `README.md`.

### Tests

Created and currently passing:

- `tests/Feature/Auth/RegistrationTest.php`
- `tests/Feature/Auth/AuthenticationTest.php`
- `tests/Feature/Auth/EmailVerificationTest.php`
- `tests/Feature/Auth/PasswordResetTest.php`
- `tests/Feature/Access/StaffAccessTest.php`
- `tests/Feature/Customer/ProfileTest.php`
- `tests/Feature/Catalogue/CatalogueTest.php`
- `tests/Feature/Admin/CatalogueManagementTest.php`
- `tests/Feature/Cart/CartTest.php`
- `tests/Feature/Checkout/CheckoutTest.php`
- `tests/Feature/Orders/OrderManagementTest.php`
- `tests/Feature/Payments/PaystackPaymentTest.php`
- `tests/Feature/Reports/SalesReportTest.php`
- `tests/Feature/Content/ContentManagementTest.php`
- `tests/Feature/Newsletter/NewsletterTest.php`
- `tests/Feature/Seo/SeoTest.php`
- `tests/Feature/Deployment/DeploymentTest.php`

Latest result:

```text
Tests:    90 passed (344 assertions)
Duration: ~16s
```

## Verification checklist

Green on 2026-11-18:

- [x] `php artisan migrate:fresh --seed --force`
- [x] `php artisan test --compact` — 90 passed, 344 assertions
- [x] `./vendor/bin/pint --test` — passed
- [x] `npm run build` — passed
- [x] `php artisan storage:link` — public storage link connected
- [x] `php artisan route:list --path=cart` — cart routes present
- [x] `php artisan route:list --path=checkout` — checkout, callback, and retry routes present
- [x] `php artisan route:list --path=orders` — customer and admin order routes present
- [x] `php artisan route:list --path=paystack` — webhook, callback, and retry routes present
- [x] `php artisan route:list --path=reports` — reports route present
- [x] `php artisan route:list --path=newsletter` — public signup/verify/unsubscribe and admin routes present
- [x] `php artisan route:list --path=sitemap` — sitemap route present
- [x] Local HTTP smoke test:
  - `/` — 200
  - `/collections` — 200
  - `/products/custom-statement-gown` — 200
  - `/cart` — 200
  - `/checkout` — 302 to login
  - `/account/orders` — 302 to login
  - `/admin/reports` — 302 to login
  - `/up` — 200
   - `POST /api/webhooks/paystack` without signature — 401
   - `/pages/about` — 200
   - `/gallery` — 200
   - `/sitemap.xml` — 200
   - `/robots.txt` — 200
   - `/admin/pages` — 302 to login

## Issues found and fixed

- `Array to string conversion` from `validateCsrfTokens(['except' => []])`.
- MySQL access denied caused by anonymous `delight`@`localhost` user shadowing the dedicated user.
- Missing Spatie permission tables because the published migration was not auto-loaded.
- Stale permission cache and duplicate permission unique-key errors during repeated seeding.
- Invalid `Event::assertDispatched` usage in the registration test.
- `Unable to locate a class or view for component [layouts.storefront|customer|admin]`.
- `RolePolicy` not resolving for `Spatie\Permission\Models\Role` in Gate checks.
- PHP formatting issues reported by Pint.
- `Call to undefined method ...::authorize()` caused by the minimal `App\Http\Controllers\Controller` skeleton; fixed by extending Laravel's base controller and using `AuthorizesRequests` and `ValidatesRequests`.
- Product image uploads during update could create multiple primary images; fixed in `ProductService::syncImages()` and covered by a regression test.
- `@props` was incorrectly used in `resources/views/customer/addresses/partials/form.blade.php` even though the file is included via `@include`; converted to plain variables and added owner-render assertions for address create/edit pages.
 - `UploadedFile::fake()->image()` fails without `gd`; tests use `createWithContent()` with `public/media/images/delight-logo.jpeg`.
 - `config()` is unavailable inside `bootstrap/app.php` middleware configuration; trusted proxies now read `TRUSTED_PROXIES` directly from `env()`.
 - Static `public/robots.txt` shadows Laravel routes; removed it so the dynamic robots response is served.
 - `email:rfc,dns` validation depends on external DNS; newsletter validation uses `email:rfc` to keep it deterministic.
 - `response()->streamDownload()` returns a Symfony streamed response; CSV export now returns a standard response so tests and clients receive body content immediately.
- Archived static README was overwritten by Laravel's default README during scaffolding; restored from Git HEAD.
- A local HTTP smoke test initially hit a stale server on port `8124`; rerun on port `8237` with correct results.

## Known limitations

- Paystack is disabled by default until real keys and the webhook URL are configured; no live credentials are stored in the repository.
- Provider-initiated refunds are not automated; refunds are recorded as order/payment status changes only.
- WhatsApp payment remains a manual handoff; no automated payment confirmation exists.
- `gd`, `intl`, and `bcmath` PHP extensions are not installed in this environment.
- Large original videos are preserved and lazy loaded; no transcoding has been configured.
- Saved addresses are not yet linked to a full address book import or delivery_zone pricing.
- Queue worker supervision, scheduler setup and database backups remain deployment-platform responsibilities.
- Newsletter delivery uses Laravel mail notifications; no external audience provider is integrated.
- The seeded super admin password in local `.env` is a development placeholder and must be changed for any deployed environment.
- No commit has been made after Phase 1, Phase 2, Phase 3, Phase 4, or Phase 5 changes.

## Next phase

The planned Phase 5 work is complete. Future work can focus on:

1. Queue worker and scheduler supervision on the deployment platform.
2. Automated database backups and restore drills.
3. External newsletter provider integration and campaign analytics.
4. A full address book and delivery-zone pricing.
5. Provider-initiated refund automation.
