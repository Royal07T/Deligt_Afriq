<x-layouts.storefront :title="$metaTitle" :description="$metaDescription" :canonical="$canonical">
    <section class="da-container py-16">
        <p class="da-eyebrow">Delight Afriq</p>
        <h1 class="da-heading mt-4">{{ $page->title }}</h1>
        @if ($page->excerpt)
            <p class="mt-5 max-w-2xl text-lg text-body">{{ $page->excerpt }}</p>
        @endif
        <div class="prose mt-10 max-w-3xl text-body [&>p]:mt-4">
            {!! App\Support\Content\PageRenderer::paragraphs($page->body) !!}
        </div>
        <div class="mt-12 flex flex-wrap gap-4">
            <a href="{{ config('brand.whatsapp_url') }}" target="_blank" rel="noreferrer" class="da-btn-primary">Chat with us</a>
            <a href="{{ route('catalogue.index') }}" class="da-btn-outline">Shop the collection</a>
        </div>
    </section>
</x-layouts.storefront>
