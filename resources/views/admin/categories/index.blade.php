<x-layouts.admin title="Categories | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Catalogue</p>
            <h1 class="da-heading mt-3">Categories</h1>
        </div>
        @can('create', App\Models\Category::class)
            <a href="{{ route('admin.categories.create') }}" class="da-btn-primary">New category</a>
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
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-ink">{{ $category->name }}</td>
                        <td class="px-5 py-4 text-body">{{ $category->slug }}</td>
                        <td class="px-5 py-4 text-body">{{ $category->products_count }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold {{ $category->is_active ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-4">
                                @can('update', $category)
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="font-semibold text-navy-950 underline underline-offset-4">Edit</a>
                                @endcan
                                @can('delete', $category)
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
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
                        <td colspan="5" class="px-5 py-6 text-center text-body">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $categories->links() }}</div>
</x-layouts.admin>
