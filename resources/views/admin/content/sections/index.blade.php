<x-layouts.admin title="Homepage sections | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">Homepage sections</h1>
        </div>
        @can('create', App\Models\HomepageSection::class)
            <a href="{{ route('admin.homepage-sections.create') }}" class="da-btn-primary">New section</a>
        @endcan
    </div>

    <div class="mt-8 overflow-x-auto da-card">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Title</th>
                    <th class="px-5 py-3">Key</th>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($sections as $section)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-ink">{{ $section->title }}</td>
                        <td class="px-5 py-4 text-body">{{ $section->key }}</td>
                        <td class="px-5 py-4 text-body">{{ $section->sort_order }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold {{ $section->is_active ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $section->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-4">
                                @can('update', $section)
                                    <a href="{{ route('admin.homepage-sections.edit', $section) }}" class="font-semibold text-navy-950 underline underline-offset-4">Edit</a>
                                @endcan
                                @can('delete', $section)
                                    <form method="POST" action="{{ route('admin.homepage-sections.destroy', $section) }}" onsubmit="return confirm('Delete this section?')">
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
                        <td colspan="5" class="px-5 py-6 text-center text-body">No homepage sections yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $sections->links() }}</div>
</x-layouts.admin>
