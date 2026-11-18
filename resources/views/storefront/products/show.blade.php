<x-layouts.storefront
    :title="$product->name.' | Delight Afriq'"
    :description="$product->short_description ?: config('brand.tagline')"
>
    <section class="da-container py-12">
        <nav class="flex flex-wrap items-center gap-2 text-xs text-body" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-navy-950">Home</a>
            <span>/</span>
            <a href="{{ route('catalogue.index') }}" class="hover:text-navy-950">Catalogue</a>
            <span>/</span>
            <a href="{{ route('catalogue.index', ['category' => $product->category?->slug]) }}" class="hover:text-navy-950">{{ $product->category?->name }}</a>
            <span>/</span>
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="mt-8 grid gap-10 lg:grid-cols-2">
            <div>
                @php
                    $gallery = $product->images->sortByDesc('is_primary');
                @endphp
                <div class="aspect-[4/5] overflow-hidden bg-cream">
                    <img src="{{ $product->primaryImage?->url ?? asset('media/images/Allin1.jpeg') }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                </div>
                @if ($gallery->count() > 1)
                    <div class="mt-4 grid grid-cols-4 gap-3">
                        @foreach ($gallery as $image)
                            <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="h-24 w-full object-cover">
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <p class="da-eyebrow">{{ $product->category?->name }}</p>
                <h1 class="mt-3 font-display text-4xl text-ink">{{ $product->name }}</h1>
                <p class="mt-4 text-sm uppercase tracking-widest text-body">SKU {{ $product->sku }}</p>

                <div class="mt-6 flex flex-wrap items-center gap-4">
                    <p class="font-display text-3xl text-navy-950">{{ $product->formattedPrice() }}</p>
                    @if ($product->compare_at_price)
                        <p class="text-lg text-body line-through">{{ $product->currency.' '.number_format((float) $product->compare_at_price, 2) }}</p>
                    @endif
                </div>

                <p class="mt-6 text-body">{{ $product->short_description }}</p>
                <p class="mt-4 text-body">{{ $product->description }}</p>

                <div class="mt-7 border-t border-line pt-6">
                    @if ($product->isAvailable())
                        <p class="text-sm font-semibold {{ $product->isLowStock() ? 'text-amber-700' : 'text-emerald-700' }}">
                            {{ $product->isLowStock() ? 'Limited stock available' : 'Available now' }}
                        </p>
                    @else
                        <p class="text-sm font-semibold text-red-700">Out of stock</p>
                    @endif
                    @if ($product->isAvailable())
                        <form method="POST" action="{{ route('cart.store') }}" class="mt-5 flex flex-wrap items-end gap-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div>
                                <label for="quantity" class="da-label">Quantity</label>
                                <input id="quantity" name="quantity" type="number" min="1" value="1" class="da-input !w-24">
                            </div>
                            <x-button variant="gold">Add to bag</x-button>
                        </form>
                    @endif
                    <a
                        href="{{ config('brand.whatsapp_url') }}?text={{ urlencode('Hello Delight Afriq, I would like to inquire about '.$product->name.' ('.$product->sku.').') }}"
                        target="_blank"
                        rel="noreferrer"
                        class="da-btn-outline mt-3"
                    >
                        Inquire on WhatsApp
                    </a>
                    <p class="mt-3 text-xs text-body">Checkout and payment handoff are coordinated through our WhatsApp concierge.</p>
                </div>

                @if ($product->collections->isNotEmpty())
                    <div class="mt-7 border-t border-line pt-6">
                        <p class="text-xs font-semibold uppercase tracking-widest text-body">Collections</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($product->collections as $collection)
                                <a href="{{ route('catalogue.index', ['collection' => $collection->slug]) }}" class="border border-line px-3 py-1.5 text-sm text-ink hover:border-gold-500">{{ $collection->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-16">
                <p class="da-eyebrow">You may also like</p>
                <h2 class="da-heading mt-3">More from {{ $product->category?->name }}</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $item)
                        @include('storefront.partials.product-card', ['product' => $item])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-layouts.storefront>
