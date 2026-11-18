<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Administration | Delight Afriq')</title>
    <link rel="icon" href="{{ asset('media/images/delight-logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-cream">
    <div x-data="{ menu: false }" class="min-h-screen lg:flex">
        <aside class="border-b border-line bg-navy-950 text-gold-100 lg:w-64 lg:shrink-0 lg:min-h-screen">
            <div class="flex items-center justify-between px-6 py-6">
                <a href="{{ route('admin.dashboard') }}" class="font-display text-xl text-white">Delight Afriq</a>
                <button type="button" class="lg:hidden" aria-label="Toggle admin menu" @click="menu = ! menu">
                    <span class="block h-0.5 w-6 bg-gold-500"></span>
                    <span class="mt-1.5 block h-0.5 w-6 bg-gold-500"></span>
                </button>
            </div>
            <nav x-show="menu" x-cloak class="flex flex-col gap-1 px-4 pb-6 lg:flex lg:px-4" aria-label="Administration navigation">
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Dashboard</a>
                <a href="{{ route('home') }}" class="px-3 py-2.5 text-sm hover:bg-white/5">View storefront</a>

                <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Operations</p>
                @can('viewAny', App\Models\Order::class)
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Orders</a>
                @endcan
                @if (auth()->user()->can(App\Support\Access\Permissions::REPORTS_VIEW))
                    <a href="{{ route('admin.reports.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.reports.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Reports</a>
                @endif

                <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Catalogue</p>
                @can('viewAny', App\Models\Category::class)
                    <a href="{{ route('admin.categories.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Categories</a>
                @endcan
                @can('viewAny', App\Models\Collection::class)
                    <a href="{{ route('admin.collections.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.collections.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Collections</a>
                @endcan
                @can('viewAny', App\Models\Product::class)
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.products.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Products</a>
                @endcan
                @can('viewAny', App\Models\Inventory::class)
                    <a href="{{ route('admin.inventory.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.inventory.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Inventory</a>
                @endcan

                <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Content</p>
                @can('viewAny', App\Models\ContentPage::class)
                    <a href="{{ route('admin.pages.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.pages.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Pages</a>
                @endcan
                @can('viewAny', App\Models\HomepageSection::class)
                    <a href="{{ route('admin.homepage-sections.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.homepage-sections.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Homepage</a>
                @endcan
                @can('viewAny', App\Models\Banner::class)
                    <a href="{{ route('admin.banners.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.banners.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Banners</a>
                @endcan
                @can('viewAny', App\Models\GalleryItem::class)
                    <a href="{{ route('admin.gallery.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.gallery.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Gallery</a>
                @endcan
                @can('viewAny', App\Models\NewsletterSubscriber::class)
                    <a href="{{ route('admin.newsletter.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('admin.newsletter.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Newsletter</a>
                @endcan

                <form method="POST" action="{{ route('admin.logout') }}" class="mt-2 border-t border-white/10 pt-4">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2.5 text-left text-sm text-gold-100 hover:bg-white/5">Sign out</button>
                </form>
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="border-b border-line bg-white px-6 py-5">
                <p class="text-xs uppercase tracking-widest text-gold-500">Administration Portal</p>
                <p class="mt-1 text-sm text-body">{{ auth()->user()->name }} · {{ auth()->user()->getRoleNames()->implode(', ') }}</p>
            </header>
            <main class="mx-auto w-full max-w-6xl px-5 py-10 sm:px-8">
                <x-alert />
                <x-alert type="error" />
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
    @stack('scripts')
</body>
</html>
