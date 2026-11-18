<x-layouts.admin title="Edit category | Delight Afriq">
    <p class="da-eyebrow">Catalogue</p>
    <h1 class="da-heading mt-3">Edit category</h1>

    <div class="mt-8">
        @include('admin.partials.taxonomy-form', [
            'model' => $category,
            'label' => 'category',
            'action' => route('admin.categories.update', $category),
            'cancelRoute' => route('admin.categories.index'),
        ])
    </div>
</x-layouts.admin>
