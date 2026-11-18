<article class="da-card group overflow-hidden">
    <a href="{{ route('products.show', $product) }}" class="block aspect-[4/5] overflow-hidden bg-cream">
        <img src="{{ $product->primaryImage?->url ?? asset('media/images/Allin1.jpeg') }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
    </a>
    <div class="p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-gold-500">{{ $product->category?->name }}</p>
        <h3 class="mt-2 font-display text-lg">
            <a href="{{ route('products.show', $product) }}" class="hover:text-gold-500">{{ $product->name }}</a>
        </h3>
        @if ($product->short_description)
            <p class="mt-2 text-sm text-body">{{ $product->short_description }}</p>
        @endif
        <div class="mt-4 flex items-center justify-between gap-3">
            <p class="font-semibold text-navy-950">{{ $product->formattedPrice() }}</p>
            @if ($product->isAvailable())
                <p class="text-xs font-semibold {{ $product->isLowStock() ? 'text-amber-700' : 'text-emerald-700' }}">
                    {{ $product->isLowStock() ? 'Low stock' : 'In stock' }}
                </p>
            @else
                <p class="text-xs font-semibold text-red-700">Out of stock</p>
            @endif
        </div>
        <a href="{{ route('products.show', $product) }}" class="mt-4 inline-block text-sm font-semibold text-navy-950 underline underline-offset-4">View piece</a>
    </div>
</article>
