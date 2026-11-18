<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $seoTitle = $metaTitle ?? $title ?? config('seo.title');
        $seoDescription = $metaDescription ?? $description ?? config('seo.description');
        $seoImage = $ogImage ?? asset(config('seo.image'));
        $seoCanonical = $canonical ?? url()->current();
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <meta name="theme-color" content="#0C1A3A">
    <link rel="icon" href="{{ asset('media/images/delight-logo.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen bg-white">
    <div x-data="{ menu: false }" @keydown.escape.window="menu = false">
        <div class="bg-navy-950 px-5 py-2.5 text-center text-xs tracking-wide text-gold-100">
            {{ config('brand.tagline') }}
        </div>

        <header class="sticky top-0 z-40 border-b border-line bg-white/95 backdrop-blur">
            <nav class="da-container flex h-20 items-center justify-between gap-6" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Delight Afriq home">
                    <img src="{{ asset('media/images/delight-logo.jpeg') }}" alt="Delight Afriq logo" class="h-11 w-11 rounded-full object-cover">
                    <span class="font-display text-lg font-bold text-navy-950">Delight Afriq</span>
                </a>

                <div class="hidden items-center gap-7 lg:flex">
                    <a href="{{ route('catalogue.index') }}" class="text-sm font-medium text-body transition hover:text-navy-950">Shop</a>
                    <a href="{{ route('home') }}#collections" class="text-sm font-medium text-body transition hover:text-navy-950">Collections</a>
                    <a href="{{ route('home') }}#bespoke" class="text-sm font-medium text-body transition hover:text-navy-950">Bespoke</a>
                    <a href="{{ route('home') }}#story" class="text-sm font-medium text-body transition hover:text-navy-950">Our Story</a>
                    <a href="{{ route('home') }}#lookbook" class="text-sm font-medium text-body transition hover:text-navy-950">Lookbook</a>
                    <a href="{{ route('gallery.index') }}" class="text-sm font-medium text-body transition hover:text-navy-950">Gallery</a>
                    <a href="{{ route('home') }}#visit" class="text-sm font-medium text-body transition hover:text-navy-950">Visit Us</a>
                </div>

                <div class="hidden items-center gap-4 lg:flex">
                    <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-navy-950 hover:text-gold-500">Bag</a>
                    <a href="{{ config('brand.whatsapp_url') }}" target="_blank" rel="noreferrer" class="text-sm font-semibold text-navy-950 hover:text-gold-500">WhatsApp</a>
                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="text-sm font-semibold text-navy-950 hover:text-gold-500">Account</a>
                        @if (auth()->user()->isStaff())
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-navy-950 hover:text-gold-500">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="da-btn-outline !px-4 !py-2">Sign out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-navy-950 hover:text-gold-500">Sign in</a>
                        <a href="{{ route('register') }}" class="da-btn-primary !px-5 !py-2.5">Join</a>
                    @endauth
                </div>

                <button type="button" class="lg:hidden" aria-label="Toggle navigation" aria-controls="mobile-navigation" :aria-expanded="menu.toString()" @click="menu = ! menu">
                    <span class="block h-0.5 w-7 bg-navy-950"></span>
                    <span class="mt-1.5 block h-0.5 w-7 bg-navy-950"></span>
                    <span class="mt-1.5 block h-0.5 w-7 bg-navy-950"></span>
                </button>
            </nav>

            <div id="mobile-navigation" x-show="menu" x-cloak class="border-t border-line bg-white lg:hidden">
                <div class="da-container flex flex-col gap-1 py-5">
                    <a href="{{ route('catalogue.index') }}" class="py-2 text-sm font-medium text-ink">Shop</a>
                    <a href="{{ route('cart.index') }}" class="py-2 text-sm font-medium text-ink">Bag</a>
                    <a href="{{ route('home') }}#collections" class="py-2 text-sm font-medium text-ink">Collections</a>
                    <a href="{{ route('home') }}#bespoke" class="py-2 text-sm font-medium text-ink">Bespoke</a>
                    <a href="{{ route('home') }}#story" class="py-2 text-sm font-medium text-ink">Our Story</a>
                    <a href="{{ route('home') }}#lookbook" class="py-2 text-sm font-medium text-ink">Lookbook</a>
                    <a href="{{ route('gallery.index') }}" class="py-2 text-sm font-medium text-ink">Gallery</a>
                    <a href="{{ route('home') }}#visit" class="py-2 text-sm font-medium text-ink">Visit Us</a>
                    <a href="{{ config('brand.whatsapp_url') }}" class="py-2 text-sm font-medium text-ink" rel="noreferrer">WhatsApp</a>
                    @auth
                        <a href="{{ route('customer.dashboard') }}" class="py-2 text-sm font-medium text-ink">Account</a>
                        <form method="POST" action="{{ route('logout') }}" class="pt-2">
                            @csrf
                            <button type="submit" class="da-btn-outline w-full">Sign out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="py-2 text-sm font-medium text-ink">Sign in</a>
                        <a href="{{ route('register') }}" class="da-btn-primary mt-2">Join Delight Afriq</a>
                    @endauth
                </div>
            </div>
        </header>

        <main>
            <div class="da-container mt-6 grid gap-3">
                <x-alert />
                <x-alert type="error" />
            </div>
            {{ $slot }}
        </main>

        <footer class="mt-10 bg-navy-950 text-gold-100">
            <div class="da-container grid gap-10 py-14 md:grid-cols-5">
                <div>
                    <p class="font-display text-2xl text-white">Delight Afriq</p>
                    <p class="mt-3 text-sm text-gold-100/70">{{ config('brand.tagline') }}</p>
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Explore</p>
                    <a href="{{ route('catalogue.index') }}" class="block py-1 text-sm hover:text-white">Shop catalogue</a>
                    <a href="{{ route('home') }}#collections" class="block py-1 text-sm hover:text-white">Collections</a>
                    <a href="{{ route('home') }}#bespoke" class="block py-1 text-sm hover:text-white">Bespoke Fashion</a>
                    <a href="{{ route('gallery.index') }}" class="block py-1 text-sm hover:text-white">Gallery</a>
                    @foreach ($footerPages ?? [] as $footerPage)
                        <a href="{{ route('pages.show', $footerPage) }}" class="block py-1 text-sm hover:text-white">{{ $footerPage->title }}</a>
                    @endforeach
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Contact</p>
                    <a href="{{ config('brand.whatsapp_url') }}" class="block py-1 text-sm hover:text-white" rel="noreferrer">{{ config('brand.whatsapp') }}</a>
                    <a href="mailto:{{ config('brand.email') }}" class="block py-1 text-sm hover:text-white">{{ config('brand.email') }}</a>
                    <a href="{{ config('brand.instagram') }}" class="block py-1 text-sm hover:text-white" target="_blank" rel="noreferrer">Instagram</a>
                </div>
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Visit</p>
                    <p class="text-sm">{{ config('brand.location') }}</p>
                    <a href="https://maps.google.com/?q=Silverbird+Gallery,+Abuja" target="_blank" rel="noreferrer" class="mt-3 inline-block text-sm text-gold-500 hover:text-white">Get directions</a>
                </div>
                <div>
                    @include('storefront.partials.newsletter-form')
                </div>
            </div>
            <div class="border-t border-white/10 py-6 text-center text-xs text-gold-100/60">
                © {{ now()->year }} Delight Afriq. All rights reserved.
            </div>
        </footer>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
