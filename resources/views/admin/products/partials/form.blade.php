@php
    $isEdit = $product->exists;
    $inventory = $product->inventory;
    $selectedCollections = array_map('strval', old('collections', $product->collections->pluck('id')->all()));
    $featured = old('is_featured', $product->is_featured ?? false);
    $trackInventory = old('inventory.track_inventory', $inventory?->track_inventory ?? true);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="da-card p-6">
        <h2 class="font-display text-xl">Product details</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <label for="category_id" class="da-label">Category</label>
                <select id="category_id" name="category_id" class="da-input" required>
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <x-form-input name="sku" label="SKU" :value="$product->sku" required />
            <x-form-input name="name" label="Name" :value="$product->name" required />
            <x-form-input name="slug" label="Slug (leave blank to generate)" :value="$product->slug" />
            <x-form-input name="price" label="Price" type="number" :value="old('price', $product->price ?? 0)" required />
            <x-form-input name="compare_at_price" label="Compare-at price" type="number" :value="$product->compare_at_price" />
            <x-form-input name="currency" label="Currency" :value="old('currency', $product->currency ?? 'NGN')" required />

            <div>
                <label for="status" class="da-label">Status</label>
                <select id="status" name="status" class="da-input" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $product->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-3 self-end pb-3 text-sm text-ink">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" @checked($featured) class="h-4 w-4">
                Feature on storefront
            </label>
        </div>

        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div>
                <label for="short_description" class="da-label">Short description</label>
                <textarea id="short_description" name="short_description" rows="3" class="da-input">{{ old('short_description', $product->short_description) }}</textarea>
                @error('short_description')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description" class="da-label">Description</label>
                <textarea id="description" name="description" rows="3" class="da-input">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="da-card p-6">
        <h2 class="font-display text-xl">Collections</h2>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($collections as $collection)
                <label class="flex items-start gap-3 border border-line p-3 text-sm text-ink">
                    <input type="checkbox" name="collections[]" value="{{ $collection->id }}" @checked(in_array((string) $collection->id, $selectedCollections, true)) class="mt-1 h-4 w-4">
                    <span>{{ $collection->name }}</span>
                </label>
            @empty
                <p class="text-sm text-body">No collections yet.</p>
            @endforelse
        </div>
        @error('collections')
            <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="da-card p-6">
        <h2 class="font-display text-xl">Inventory</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <x-form-input name="inventory[quantity]" label="Stock on hand" type="number" :value="old('inventory.quantity', $inventory?->quantity ?? 0)" required />
            <x-form-input name="inventory[reserved]" label="Reserved" type="number" :value="old('inventory.reserved', $inventory?->reserved ?? 0)" />
            <x-form-input name="inventory[low_stock_threshold]" label="Low stock threshold" type="number" :value="old('inventory.low_stock_threshold', $inventory?->low_stock_threshold ?? 5)" />
            <label class="flex items-center gap-3 self-end pb-3 text-sm text-ink">
                <input type="hidden" name="inventory[track_inventory]" value="0">
                <input type="checkbox" name="inventory[track_inventory]" value="1" @checked($trackInventory) class="h-4 w-4">
                Track stock
            </label>
        </div>
    </div>

    <div class="da-card p-6">
        <h2 class="font-display text-xl">Images</h2>

        @if ($isEdit && $product->images->isNotEmpty())
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($product->images as $image)
                    <div class="border border-line p-3">
                        <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="h-32 w-full object-cover">
                        <div class="mt-3 space-y-2 text-xs text-ink">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked(old('primary_image_id', $product->primaryImage?->id) == $image->id)>
                                Primary
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}">
                                Remove
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-5">
            <label for="images" class="da-label">Upload images</label>
            <input id="images" type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" class="da-input">
            @error('images')
                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
            @enderror
            @error('images.*')
                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-4">
        <x-button>{{ $isEdit ? 'Save changes' : 'Create product' }}</x-button>
        <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-navy-950 underline underline-offset-4">Cancel</a>
    </div>
</form>
