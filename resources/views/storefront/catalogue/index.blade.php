<x-layouts.storefront
    title="Shop the collection | Delight Afriq"
    description="Browse ready-to-wear pieces, bespoke fashion and statement accessories from Delight Afriq."
>
    <section class="da-container py-14">
        <p class="da-eyebrow">Catalogue</p>
        <h1 class="da-heading mt-3">Shop the collection</h1>
        <p class="mt-3 max-w-2xl text-body">Explore African heritage designs, modern silhouettes and statement accessories from the Delight Afriq atelier.</p>

        <form method="GET" action="{{ route('catalogue.index') }}" class="da-card mt-8 grid gap-4 p-5 md:grid-cols-4">
            <x-form-input name="search" label="Search" :value="$filters['search']" />
            <div>
                <label for="category" class="da-label">Category</label>
                <select id="category" name="category" class="da-input">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($filters['category'] === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="collection" class="da-label">Collection</label>
                <select id="collection" name="collection" class="da-input">
                    <option value="">All collections</option>
                    @foreach ($collections as $collection)
                        <option value="{{ $collection->slug }}" @selected($filters['collection'] === $collection->slug)>{{ $collection->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="sort" class="da-label">Sort by</label>
                <select id="sort" name="sort" class="da-input">
                    <option value="">Featured</option>
                    <option value="name" @selected($filters['sort'] === 'name')>Name</option>
                    <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Price: low to high</option>
                    <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Price: high to low</option>
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-4 md:col-span-4">
                <x-button>Apply filters</x-button>
                @if ($filters['search'] || $filters['category'] || $filters['collection'] || $filters['sort'])
                    <a href="{{ route('catalogue.index') }}" class="text-sm font-semibold text-navy-950 underline underline-offset-4">Clear filters</a>
                @endif
            </div>
        </form>

        @if ($products->isEmpty())
            <div class="da-card mt-8 p-10 text-center">
                <p class="font-display text-xl">No pieces found</p>
                <p class="mt-2 text-sm text-body">Try a different search or clear the filters.</p>
            </div>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            <div class="mt-10">{{ $products->links() }}</div>
        @endif
    </section>
</x-layouts.storefront>
