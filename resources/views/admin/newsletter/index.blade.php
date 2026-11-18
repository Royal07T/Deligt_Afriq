<x-layouts.admin title="Newsletter | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">Content</p>
            <h1 class="da-heading mt-3">Newsletter subscribers</h1>
        </div>
        <a href="{{ route('admin.newsletter.export', request()->only('status')) }}" class="da-btn-outline">Export CSV</a>
    </div>

    <form method="GET" action="{{ route('admin.newsletter.index') }}" class="da-card mt-8 grid gap-4 p-5 sm:grid-cols-[1fr_200px_auto]">
        <x-form-input name="search" label="Search" :value="$search" />
        <div>
            <label for="status" class="da-label">Status</label>
            <select id="status" name="status" class="da-input">
                <option value="">All</option>
                @foreach ([App\Models\NewsletterSubscriber::PENDING, App\Models\NewsletterSubscriber::SUBSCRIBED, App\Models\NewsletterSubscriber::UNSUBSCRIBED] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <x-button type="submit" variant="gold">Filter</x-button>
        </div>
    </form>

    <div class="mt-8 overflow-x-auto da-card">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Subscribed</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($subscribers as $subscriber)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-ink">{{ $subscriber->name ?: '—' }}</td>
                        <td class="px-5 py-4 text-body">{{ $subscriber->email }}</td>
                        <td class="px-5 py-4 text-body">{{ ucfirst($subscriber->status) }}</td>
                        <td class="px-5 py-4 text-body">{{ $subscriber->subscribed_at?->format('Y-m-d') ?: '—' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end">
                                @can('delete', $subscriber)
                                    <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm('Remove this subscriber?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-700 underline underline-offset-4">Remove</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-body">No subscribers yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $subscribers->links() }}</div>
</x-layouts.admin>
