<x-layouts.admin title="Administration dashboard | Delight Afriq">
    <p class="da-eyebrow">Overview</p>
    <h1 class="da-heading mt-3">Administration dashboard</h1>
    <p class="mt-3 text-body">Catalogue, orders, payments, and reporting are live. Monitor paid revenue and operational activity from one place.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-7">
        @foreach (['Customers' => $stats['customers'], 'Staff accounts' => $stats['staff'], 'Roles' => $stats['roles'], 'Permissions' => $stats['permissions'], 'Products' => $stats['products'], 'Orders' => $stats['orders'], 'Paid revenue' => $stats['revenue']] as $label => $value)
            <div class="da-card p-5">
                <p class="text-xs uppercase tracking-widest text-gold-500">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl text-ink">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="da-card p-6">
            <h2 class="font-display text-xl">Your access</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($permissions as $permission)
                    <span class="border border-line bg-cream px-2.5 py-1 text-xs text-ink">{{ $permission }}</span>
                @empty
                    <p class="text-sm text-body">No module permissions assigned.</p>
                @endforelse
            </div>
        </div>
        <div class="da-card p-6">
            <h2 class="font-display text-xl">Recent activity</h2>
            @if ($activity->isEmpty())
                <p class="mt-4 text-sm text-body">No activity recorded yet.</p>
            @else
                <ul class="mt-4 divide-y divide-line text-sm">
                    @foreach ($activity as $item)
                        <li class="py-2.5">
                            <span class="font-semibold text-ink">{{ $item->action }}</span>
                            <span class="block text-xs text-body">{{ $item->user?->name ?? 'System' }} · {{ $item->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layouts.admin>
