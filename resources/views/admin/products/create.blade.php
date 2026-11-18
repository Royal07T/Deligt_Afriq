<x-layouts.admin title="New product | Delight Afriq">
    <p class="da-eyebrow">Catalogue</p>
    <h1 class="da-heading mt-3">New product</h1>

    <div class="mt-8">
        @include('admin.products.partials.form', [
            'product' => $product,
            'categories' => $categories,
            'collections' => $collections,
            'statuses' => $statuses,
            'action' => route('admin.products.store'),
        ])
    </div>
</x-layouts.admin>
