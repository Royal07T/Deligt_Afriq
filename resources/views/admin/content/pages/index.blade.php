<x-layouts.admin title="Pages | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">Pages</h1>
        </div>
        @can('create', App\Models\ContentPage::class)
            <a href="{{ route('admin.pages.create') }}" class="da-btn-primary">New page</a>
        @endcan
    </div>

    <div class="mt-8 overflow-x-auto da-card">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Title</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Updated</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($pages as $page)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-ink">{{ $page->title }}</td>
                        <td class="px-5 py-4 text-body">{{ $page->slug }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-semibold {{ $page->is_published ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-body">{{ $page->updated_at->format('Y-m-d') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-4">
                                @can('update', $page)
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="font-semibold text-navy-950 underline underline-offset-4">Edit</a>
                                @endcan
                                @if ($page->is_published)
                                    <a href="{{ route('pages.show', $page) }}" class="font-semibold text-navy-950 underline underline-offset-4">View</a>
                                @endif
                                @can('delete', $page)
                                    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">
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
                        <td colspan="5" class="px-5 py-6 text-center text-body">No pages yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $pages->links() }}</div>
</x-layouts.admin>
