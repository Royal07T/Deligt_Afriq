<x-layouts.storefront
    :meta-title="$metaTitle"
    :meta-description="$metaDescription"
    :canonical="$canonical"
>
    <section class="relative overflow-hidden bg-navy-950">
        <div class="da-container grid items-center gap-10 py-16 lg:grid-cols-2 lg:py-24">
            <div class="relative z-10">
                <p class="da-eyebrow text-gold-400">Delight Afriq</p>
                <h1 class="mt-5 font-display text-4xl leading-tight text-white sm:text-5xl lg:text-6xl">
                    AFRICAN HERITAGE.<br><span class="text-gold-400">MODERN LUXURY.</span>
                </h1>
                <p class="mt-6 max-w-lg text-base leading-relaxed text-gold-100/80">
                    Discover bespoke fashion and statement pieces crafted for those who want to stand out with confidence, elegance and intention.
                </p>
                <div class="mt-9 flex flex-wrap gap-4">
                    <a href="#collections" class="da-btn-gold">Explore Collections</a>
                    <a href="{{ config('brand.whatsapp_url') }}?text={{ urlencode('Hello Delight Afriq, I would like to book a fashion consultation.') }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center border border-gold-500 px-6 py-3 text-sm font-semibold text-gold-400 transition hover:bg-gold-500 hover:text-navy-950">Book a Consultation</a>
                </div>
            </div>
            <div class="relative aspect-[4/5] w-full overflow-hidden">
                <video class="h-full w-full object-cover" autoplay muted loop playsinline preload="metadata" aria-label="Delight Afriq editorial film">
                    <source src="{{ asset('media/videos/'.rawurlencode('landingpage vide.mp4')) }}" type="video/mp4">
                </video>
            </div>
        </div>
    </section>

    @if ($banners->isNotEmpty())
        <section class="da-container py-10">
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($banners as $banner)
                    <article class="relative overflow-hidden rounded-2xl bg-navy-950 text-gold-100">
                        <img src="{{ App\Support\Media\MediaUrl::for($banner->image_path) }}" alt="{{ $banner->title }}" class="h-64 w-full object-cover opacity-70" loading="lazy">
                        <div class="absolute inset-0 flex flex-col justify-end gap-2 bg-gradient-to-t from-navy-950 to-transparent p-6">
                            <h2 class="font-display text-2xl text-white">{{ $banner->title }}</h2>
                            @if ($banner->subtitle)
                                <p class="text-sm text-gold-100/80">{{ $banner->subtitle }}</p>
                            @endif
                            @if ($banner->cta_url && $banner->cta_label)
                                <a href="{{ $banner->cta_url }}" class="mt-2 w-fit text-sm font-semibold text-gold-400 underline underline-offset-4">{{ $banner->cta_label }}</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section id="collections" class="da-section da-container scroll-mt-24">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <p class="da-eyebrow">Collections</p>
                <h2 class="da-heading mt-3">Explore Our Categories</h2>
            </div>
            <a href="{{ route('catalogue.index') }}" class="da-btn-outline">Shop all pieces</a>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($categories as $category)
                <article class="da-card group overflow-hidden">
                    <div class="aspect-square overflow-hidden bg-cream">
                        <img src="{{ $category->image_path ? asset($category->image_path) : asset('media/images/Allin1.jpeg') }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                    </div>
                    <div class="p-5">
                        <h3 class="font-display text-lg">{{ $category->name }}</h3>
                        <p class="mt-2 text-sm text-body">{{ $category->description }}</p>
                        <a href="{{ route('catalogue.index', ['category' => $category->slug]) }}" class="mt-4 inline-block text-sm font-semibold text-gold-500 underline underline-offset-4">Browse category</a>
                    </div>
                </article>
            @empty
                <p class="text-body">Categories will appear here once they are added.</p>
            @endforelse
        </div>
    </section>

    @if ($featuredProducts->isNotEmpty())
        <section class="da-section bg-cream">
            <div class="da-container">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="da-eyebrow">Featured pieces</p>
                        <h2 class="da-heading mt-3">Made to be remembered</h2>
                    </div>
                    <a href="{{ route('catalogue.index') }}" class="da-btn-outline">View catalogue</a>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredProducts as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @foreach ($sections as $section)
        <section id="{{ $section->key }}" class="da-section da-container scroll-mt-24">
            <div class="max-w-3xl">
                <p class="da-eyebrow">Delight Afriq</p>
                <h2 class="da-heading mt-3">{{ $section->title }}</h2>
                <div class="mt-6 text-body [&>p]:mt-4">
                    {!! App\Support\Content\PageRenderer::paragraphs($section->body) !!}
                </div>
            </div>
        </section>
    @endforeach

    <section id="story" class="da-section scroll-mt-24 bg-cream">
        <div class="da-container grid gap-12 lg:grid-cols-2">
            <div>
                <p class="da-eyebrow">Our Story</p>
                <h2 class="da-heading mt-3">The Delight Afriq Story</h2>
                <p class="mt-6 text-body">Rooted in African heritage and crafted for contemporary elegance, Delight Afriq represents the intersection of tradition and modern luxury fashion.</p>
                <p class="mt-4 text-body">From bespoke occasion wear to refined ready-to-wear collections, our designs are crafted for individuals who seek more than clothing — they seek an expression of identity, confidence and cultural connection.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <img src="{{ asset('media/images/'.rawurlencode('Delight CEO.jpeg')) }}" alt="Delight Afriq creative director" class="h-64 w-full object-cover sm:h-full" loading="lazy">
                <img src="{{ asset('media/images/'.rawurlencode('Modern Heritage Set.png')) }}" alt="Modern Heritage Set" class="h-64 w-full object-cover sm:h-full" loading="lazy">
                <img src="{{ asset('media/images/'.rawurlencode('Allin1.jpeg')) }}" alt="Delight Afriq collection" class="h-64 w-full object-cover sm:h-full" loading="lazy">
            </div>
        </div>
    </section>

    <section id="lookbook" class="da-section da-container scroll-mt-24">
        <div class="mb-10 max-w-2xl">
            <p class="da-eyebrow">Lookbook</p>
            <h2 class="da-heading mt-3">Editorial Gallery</h2>
            <p class="mt-3 text-body">Explore our collection through our visual editorial showcase.</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <video class="h-72 w-full object-cover" muted loop playsinline preload="none" controls aria-label="Delight Afriq editorial film">
                <source src="{{ asset('media/videos/'.rawurlencode('Deligt vid 2.mp4')) }}" type="video/mp4">
            </video>
            <img src="{{ asset('media/images/'.rawurlencode('Ankara shoe pair.png')) }}" alt="Ankara shoes" class="h-72 w-full object-cover" loading="lazy">
            <img src="{{ asset('media/images/'.rawurlencode('Custom Statement Gown1.png')) }}" alt="Custom statement gown" class="h-72 w-full object-cover" loading="lazy">
            <video class="h-72 w-full object-cover" muted loop playsinline preload="none" controls aria-label="Delight Afriq lookbook film">
                <source src="{{ asset('media/videos/vid2.mp4') }}" type="video/mp4">
            </video>
            <video class="h-72 w-full object-cover" muted loop playsinline preload="none" controls aria-label="Delight Afriq runway film">
                <source src="{{ asset('media/videos/vid3.mp4') }}" type="video/mp4">
            </video>
            <video class="h-72 w-full object-cover" muted loop playsinline preload="none" controls aria-label="Delight Afriq collection film">
                <source src="{{ asset('media/videos/video1.mp4') }}" type="video/mp4">
            </video>
        </div>
        @if ($galleryPreview->isNotEmpty())
            <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
                <p class="text-body">See more from the Delight Afriq studio.</p>
                <a href="{{ route('gallery.index') }}" class="da-btn-outline">Open the gallery</a>
            </div>
        @endif
    </section>

    <section id="bespoke" class="da-section scroll-mt-24 bg-navy-950 text-gold-100">
        <div class="da-container grid gap-10 lg:grid-cols-2">
            <div>
                <p class="da-eyebrow text-gold-400">Bespoke Service</p>
                <h2 class="mt-3 font-display text-3xl text-white sm:text-4xl">Made for You</h2>
                <p class="mt-5 text-gold-100/75">Work with Delight Afriq to create a custom piece that reflects your unique style, event and vision — from fabric selection to the final fitting.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach (['Weddings & Celebrations', 'Personal Aesthetic', 'Premium Materials', 'Custom Tailoring'] as $offering)
                    <div class="border border-white/15 p-5">
                        <h3 class="font-display text-lg text-white">{{ $offering }}</h3>
                        <p class="mt-2 text-sm text-gold-100/70">Consultation, craftsmanship and attention to every detail.</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="visit" class="da-section da-container scroll-mt-24">
        <div class="grid gap-8 md:grid-cols-2">
            <div class="da-card p-8">
                <p class="da-eyebrow">Visit Us</p>
                <h2 class="da-heading mt-3">Silverbird Gallery</h2>
                <p class="mt-3 text-body">Abuja, Nigeria</p>
                <a href="https://maps.google.com/?q=Silverbird+Gallery,+Abuja" target="_blank" rel="noreferrer" class="da-btn-outline mt-6">Get directions</a>
            </div>
            <div class="da-card p-8">
                <p class="da-eyebrow">Connect</p>
                <h2 class="da-heading mt-3">Get in Touch</h2>
                <p class="mt-3 text-body">Reach out through WhatsApp or Instagram for inquiries, consultations and orders.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ config('brand.whatsapp_url') }}" target="_blank" rel="noreferrer" class="da-btn-primary">WhatsApp</a>
                    <a href="{{ config('brand.instagram') }}" target="_blank" rel="noreferrer" class="da-btn-outline">Instagram</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.storefront>
