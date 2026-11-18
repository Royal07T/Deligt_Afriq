<x-layouts.admin title="New collection | Delight Afriq">
    <p class="da-eyebrow">Catalogue</p>
    <h1 class="da-heading mt-3">New collection</h1>

    <div class="mt-8">
        @include('admin.partials.taxonomy-form', [
            'model' => $collection,
            'label' => 'collection',
            'action' => route('admin.collections.store'),
            'cancelRoute' => route('admin.collections.index'),
        ])
    </div>
</x-layouts.admin>
