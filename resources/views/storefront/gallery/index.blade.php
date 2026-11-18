<x-layouts.storefront :title="$metaTitle" :description="$metaDescription" :canonical="$canonical">
    <section class="da-container py-16">
        <p class="da-eyebrow">Lookbook</p>
        <h1 class="da-heading mt-4">The Delight Gallery</h1>
        <p class="mt-5 max-w-2xl text-lg text-body">Recent silhouettes, accessories and atelier moments from our Abuja studio.</p>

        @if ($items->isEmpty())
            <p class="mt-10 text-body">The gallery is being updated. Please check back soon.</p>
        @else
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $item)
                    <figure class="overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
                        <img src="{{ App\Support\Media\MediaUrl::for($item->image_path) }}" alt="{{ $item->title }}" class="h-72 w-full object-cover" loading="lazy">
                        <figcaption class="px-5 py-4">
                            <p class="font-semibold text-ink">{{ $item->title }}</p>
                            @if ($item->category)
                                <p class="mt-1 text-xs uppercase tracking-widest text-gold-500">{{ $item->category }}</p>
                            @endif
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.storefront>
