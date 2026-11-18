<x-layouts.admin title="Collections | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Catalogue</p>
            <h1 class="da-heading mt-3">Collections</h1>
        </div>
        @can('create', App\Models\Collection::class)
            <a href="{{ route('admin.collections.create') }}" class="da-btn-primary">New collection</a>
        @endcan
    </div>

    <div class="mt-8 overflow-x-auto da-card">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Products</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($collections as $collection)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-ink">{{ $collection->name }}</td>
                        <td class="px-5 py-4 text-body">{{ $collection->slug }}</td>
                        <td class="px-5 py-4 text-body">{{ $collection->products_count }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold {{ $collection->is_active ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $collection->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-4">
                                @can('update', $collection)
                                    <a href="{{ route('admin.collections.edit', $collection) }}" class="font-semibold text-navy-950 underline underline-offset-4">Edit</a>
                                @endcan
                                @can('delete', $collection)
                                    <form method="POST" action="{{ route('admin.collections.destroy', $collection) }}" onsubmit="return confirm('Delete this collection?')">
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
                        <td colspan="5" class="px-5 py-6 text-center text-body">No collections yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $collections->links() }}</div>
</x-layouts.admin>
