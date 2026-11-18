<x-layouts.admin title="Products | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Catalogue</p>
            <h1 class="da-heading mt-3">Products</h1>
        </div>
        @can('create', App\Models\Product::class)
            <a href="{{ route('admin.products.create') }}" class="da-btn-primary">New product</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('admin.products.index') }}" class="da-card mt-8 grid gap-4 p-5 md:grid-cols-4">
        <x-form-input name="search" label="Search" :value="request('search')" />
        <div>
            <label for="category" class="da-label">Category</label>
            <select id="category" name="category" class="da-input">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="da-label">Status</label>
            <select id="status" name="status" class="da-input">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <x-button variant="outline">Apply filters</x-button>
        </div>
    </form>

    <div class="mt-6 overflow-x-auto da-card">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Product</th>
                    <th class="px-5 py-3">Category</th>
                    <th class="px-5 py-3">Price</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Stock</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->primaryImage?->url ?? asset('media/images/Allin1.jpeg') }}" alt="{{ $product->name }}" class="h-12 w-12 object-cover">
                                <div>
                                    <p class="font-semibold text-ink">{{ $product->name }}</p>
                                    <p class="text-xs text-body">{{ $product->sku }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-body">{{ $product->category?->name }}</td>
                        <td class="px-5 py-4 text-body">{{ $product->formattedPrice() }}</td>
                        <td class="px-5 py-4 text-xs font-semibold uppercase text-body">{{ $product->status }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold {{ $product->isAvailable() ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $product->isAvailable() ? ($product->isLowStock() ? 'Low stock' : 'In stock') : 'Out of stock' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-4">
                                @can('update', $product)
                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-navy-950 underline underline-offset-4">Edit</a>
                                @endcan
                                @can('manageInventory', $product)
                                    <a href="{{ route('admin.inventory.edit', $product) }}" class="font-semibold text-navy-950 underline underline-offset-4">Stock</a>
                                @endcan
                                @can('delete', $product)
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-700 underline underline-offset-4">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-6 text-center text-body">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $products->links() }}</div>
</x-layouts.admin>
