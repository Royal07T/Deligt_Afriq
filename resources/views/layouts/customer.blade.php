<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'My Account | Delight Afriq')</title>
    <link rel="icon" href="{{ asset('media/images/delight-logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-cream">
    <div x-data="{ menu: false }" class="min-h-screen lg:flex">
        <aside class="border-b border-line bg-navy-950 text-gold-100 lg:w-64 lg:shrink-0 lg:border-b-0 lg:min-h-screen">
            <div class="flex items-center justify-between px-6 py-6">
                <a href="{{ route('home') }}" class="font-display text-xl text-white">Delight Afriq</a>
                <button type="button" class="lg:hidden" aria-label="Toggle account menu" @click="menu = ! menu">
                    <span class="block h-0.5 w-6 bg-gold-500"></span>
                    <span class="mt-1.5 block h-0.5 w-6 bg-gold-500"></span>
                </button>
            </div>
            <nav x-show="menu" x-cloak class="flex flex-col gap-1 px-4 pb-6 lg:flex lg:px-4" aria-label="Account navigation">
                <a href="{{ route('customer.dashboard') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('customer.dashboard') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Overview</a>
                <a href="{{ route('customer.orders.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('customer.orders.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Orders</a>
                <a href="{{ route('customer.addresses.index') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('customer.addresses.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Addresses</a>
                <a href="{{ route('customer.profile.edit') }}" class="px-3 py-2.5 text-sm {{ request()->routeIs('customer.profile.*') ? 'bg-white/10 text-white' : 'hover:bg-white/5' }}">Profile &amp; preferences</a>
                @if (auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2.5 text-sm hover:bg-white/5">Administration</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-white/10 pt-4">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2.5 text-left text-sm text-gold-100 hover:bg-white/5">Sign out</button>
                </form>
            </nav>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="border-b border-line bg-white px-6 py-5">
                <p class="text-xs uppercase tracking-widest text-gold-500">Customer Portal</p>
                <p class="mt-1 text-sm text-body">Signed in as <span class="font-semibold text-ink">{{ auth()->user()->name }}</span></p>
            </header>
            <main class="da-container py-10">
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
