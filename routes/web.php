<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\ContentPageController as AdminContentPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GalleryItemController as AdminGalleryItemController;
use App\Http\Controllers\Admin\HomepageSectionController as AdminHomepageSectionController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\StaffSessionController;
use App\Http\Controllers\Customer\AddressController as CustomerAddressController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CatalogueController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\GalleryController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\NewsletterController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\SeoController;
use App\Support\Access\Permissions;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/collections', [CatalogueController::class, 'index'])->name('catalogue.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('newsletter.verify');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('newsletter.unsubscribe');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/admin/login', [StaffSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [StaffSessionController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/admin/logout', [StaffSessionController::class, 'destroy'])
    ->middleware(['auth', 'staff'])
    ->name('admin.logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

Route::prefix('checkout')->middleware(['auth', 'active', 'verified'])->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
    Route::get('/paystack/callback', [CheckoutController::class, 'callback'])
        ->middleware('throttle:10,1')
        ->name('checkout.paystack.callback');
    Route::post('/{order}/paystack/retry', [CheckoutController::class, 'retry'])
        ->middleware('throttle:10,1')
        ->name('checkout.paystack.retry');
    Route::get('/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
});

Route::prefix('account')->middleware(['auth', 'active', 'verified'])->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('customer.password.update');

    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('customer.orders.cancel');

    Route::get('/addresses', [CustomerAddressController::class, 'index'])->name('customer.addresses.index');
    Route::get('/addresses/create', [CustomerAddressController::class, 'create'])->name('customer.addresses.create');
    Route::post('/addresses', [CustomerAddressController::class, 'store'])->name('customer.addresses.store');
    Route::get('/addresses/{address}/edit', [CustomerAddressController::class, 'edit'])->name('customer.addresses.edit');
    Route::put('/addresses/{address}', [CustomerAddressController::class, 'update'])->name('customer.addresses.update');
    Route::delete('/addresses/{address}', [CustomerAddressController::class, 'destroy'])->name('customer.addresses.destroy');
    Route::post('/addresses/{address}/default', [CustomerAddressController::class, 'default'])->name('customer.addresses.default');
});

Route::prefix('admin')->middleware(['auth', 'active', 'staff'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/categories', [AdminCategoryController::class, 'index'])
        ->middleware('permission:'.Permissions::CATEGORIES_VIEW)
        ->name('admin.categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])
        ->middleware('permission:'.Permissions::CATEGORIES_CREATE)
        ->name('admin.categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])
        ->middleware('permission:'.Permissions::CATEGORIES_CREATE)
        ->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])
        ->middleware('permission:'.Permissions::CATEGORIES_EDIT)
        ->name('admin.categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])
        ->middleware('permission:'.Permissions::CATEGORIES_EDIT)
        ->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])
        ->middleware('permission:'.Permissions::CATEGORIES_DELETE)
        ->name('admin.categories.destroy');

    Route::get('/collections', [AdminCollectionController::class, 'index'])
        ->middleware('permission:'.Permissions::COLLECTIONS_VIEW)
        ->name('admin.collections.index');
    Route::get('/collections/create', [AdminCollectionController::class, 'create'])
        ->middleware('permission:'.Permissions::COLLECTIONS_CREATE)
        ->name('admin.collections.create');
    Route::post('/collections', [AdminCollectionController::class, 'store'])
        ->middleware('permission:'.Permissions::COLLECTIONS_CREATE)
        ->name('admin.collections.store');
    Route::get('/collections/{collection}/edit', [AdminCollectionController::class, 'edit'])
        ->middleware('permission:'.Permissions::COLLECTIONS_EDIT)
        ->name('admin.collections.edit');
    Route::put('/collections/{collection}', [AdminCollectionController::class, 'update'])
        ->middleware('permission:'.Permissions::COLLECTIONS_EDIT)
        ->name('admin.collections.update');
    Route::delete('/collections/{collection}', [AdminCollectionController::class, 'destroy'])
        ->middleware('permission:'.Permissions::COLLECTIONS_DELETE)
        ->name('admin.collections.destroy');

    Route::get('/products', [AdminProductController::class, 'index'])
        ->middleware('permission:'.Permissions::PRODUCTS_VIEW)
        ->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])
        ->middleware('permission:'.Permissions::PRODUCTS_CREATE)
        ->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])
        ->middleware('permission:'.Permissions::PRODUCTS_CREATE)
        ->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])
        ->middleware('permission:'.Permissions::PRODUCTS_EDIT)
        ->name('admin.products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])
        ->middleware('permission:'.Permissions::PRODUCTS_EDIT)
        ->name('admin.products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])
        ->middleware('permission:'.Permissions::PRODUCTS_DELETE)
        ->name('admin.products.destroy');

    Route::get('/inventory', [AdminInventoryController::class, 'index'])
        ->middleware('permission:'.Permissions::INVENTORY_VIEW)
        ->name('admin.inventory.index');
    Route::get('/inventory/{product}/edit', [AdminInventoryController::class, 'edit'])
        ->middleware('permission:'.Permissions::INVENTORY_MANAGE)
        ->name('admin.inventory.edit');
    Route::put('/inventory/{product}', [AdminInventoryController::class, 'update'])
        ->middleware('permission:'.Permissions::INVENTORY_MANAGE)
        ->name('admin.inventory.update');

    Route::get('/orders', [AdminOrderController::class, 'index'])
        ->middleware('permission:'.Permissions::ORDERS_VIEW)
        ->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
        ->middleware('permission:'.Permissions::ORDERS_VIEW)
        ->name('admin.orders.show');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->middleware('permission:'.Permissions::ORDERS_UPDATE)
        ->name('admin.orders.update-status');

    Route::get('/reports', [AdminReportController::class, 'index'])
        ->middleware('permission:'.Permissions::REPORTS_VIEW)
        ->name('admin.reports.index');

    Route::resource('pages', AdminContentPageController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('admin.pages');
    Route::resource('banners', AdminBannerController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('admin.banners');
    Route::resource('homepage-sections', AdminHomepageSectionController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->parameters(['homepage-sections' => 'homepageSection'])
        ->names('admin.homepage-sections');
    Route::resource('gallery', AdminGalleryItemController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->parameters(['gallery' => 'galleryItem'])
        ->names('admin.gallery');

    Route::get('/newsletter', [AdminNewsletterController::class, 'index'])
        ->name('admin.newsletter.index');
    Route::get('/newsletter/export', [AdminNewsletterController::class, 'export'])
        ->name('admin.newsletter.export');
    Route::delete('/newsletter/{newsletterSubscriber}', [AdminNewsletterController::class, 'destroy'])
        ->name('admin.newsletter.destroy');
});
