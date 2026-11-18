<x-layouts.admin title="Adjust inventory | Delight Afriq">
    <p class="da-eyebrow">Catalogue</p>
    <h1 class="da-heading mt-3">Adjust inventory</h1>
    <p class="mt-3 text-body">{{ $product->name }} · {{ $product->sku }}</p>

    <div class="da-card mt-8 max-w-2xl p-6">
        <div class="grid gap-4 border-b border-line pb-6 sm:grid-cols-3">
            <div>
                <p class="text-xs uppercase tracking-widest text-body">On hand</p>
                <p class="mt-1 font-display text-2xl text-ink">{{ $inventory->quantity }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest text-body">Reserved</p>
                <p class="mt-1 font-display text-2xl text-ink">{{ $inventory->reserved }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest text-body">Available</p>
                <p class="mt-1 font-display text-2xl text-ink">{{ $inventory->availableQuantity() }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.inventory.update', $product) }}" class="mt-6 grid gap-6 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <x-form-input name="quantity" label="Set quantity" type="number" :value="old('quantity', $inventory->quantity)" />
            <x-form-input name="adjustment" label="Or adjust by" type="number" :value="old('adjustment')" />
            <x-form-input name="reserved" label="Reserved" type="number" :value="old('reserved', $inventory->reserved)" />
            <x-form-input name="low_stock_threshold" label="Low stock threshold" type="number" :value="old('low_stock_threshold', $inventory->low_stock_threshold)" />
            <label class="flex items-center gap-3 sm:col-span-2 text-sm text-ink">
                <input type="hidden" name="track_inventory" value="0">
                <input type="checkbox" name="track_inventory" value="1" @checked(old('track_inventory', $inventory->track_inventory)) class="h-4 w-4">
                Track stock for this product
            </label>

            <div class="flex flex-wrap items-center gap-4 sm:col-span-2">
                <x-button>Save inventory</x-button>
                <a href="{{ route('admin.inventory.index') }}" class="text-sm font-semibold text-navy-950 underline underline-offset-4">Back to inventory</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
