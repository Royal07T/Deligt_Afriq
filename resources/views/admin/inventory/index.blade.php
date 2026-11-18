<x-layouts.admin title="Inventory | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Catalogue</p>
            <h1 class="da-heading mt-3">Inventory</h1>
        </div>
        <form method="GET" action="{{ route('admin.inventory.index') }}">
            <label class="flex items-center gap-2 text-sm text-ink">
                <input type="hidden" name="low" value="0">
                <input type="checkbox" name="low" value="1" @checked(request('low')) class="h-4 w-4">
                Low stock only
            </label>
            <x-button variant="outline" class="ml-3">Apply</x-button>
        </form>
    </div>

    <div class="mt-8 overflow-x-auto da-card">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">On hand</th>
                    <th class="px-5 py-3">Reserved</th>
                    <th class="px-5 py-3">Available</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-ink">{{ $product->name }}</p>
                            <p class="text-xs text-body">{{ $product->sku }}</p>
                        </td>
                        <td class="px-5 py-4 text-body">{{ $product->category?->name }}</td>
                        <td class="px-5 py-4 text-body">{{ $product->inventory->quantity }}</td>
                        <td class="px-5 py-4 text-body">{{ $product->inventory->reserved }}</td>
                        <td class="px-5 py-4 text-body">{{ $product->inventory->availableQuantity() }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold {{ $product->isAvailable() ? ($product->isLowStock() ? 'text-amber-700' : 'text-emerald-700') : 'text-red-700' }}">
                                {{ $product->isAvailable() ? ($product->isLowStock() ? 'Low stock' : 'In stock') : 'Out of stock' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @can('manageInventory', $product)
                                <a href="{{ route('admin.inventory.edit', $product) }}" class="font-semibold text-navy-950 underline underline-offset-4">Adjust</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-6 text-center text-body">No inventory records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
</x-layouts.admin>
