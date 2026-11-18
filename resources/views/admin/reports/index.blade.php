<x-layouts.admin title="Sales reports | Delight Afriq">
    <p class="da-eyebrow">Insights</p>
    <h1 class="da-heading mt-3">Sales reports</h1>
    <p class="mt-3 text-body">Paid revenue, order volume, and best sellers across the selected period.</p>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="da-card mt-8 grid gap-4 p-5 sm:grid-cols-3">
        <div>
            <label for="from" class="da-label">From</label>
            <input id="from" type="date" name="from" value="{{ $from }}" class="da-input">
        </div>
        <div>
            <label for="to" class="da-label">To</label>
            <input id="to" type="date" name="to" value="{{ $to }}" class="da-input">
        </div>
        <div class="flex items-end gap-3">
            <x-button variant="outline">Apply</x-button>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-body underline">Clear</a>
        </div>
    </form>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            'Paid revenue' => App\Support\Commerce\Money::format($summary['revenue_minor'], config('store.currency', 'NGN')),
            'Paid orders' => $summary['paid_orders'],
            'Pending payment' => $summary['pending_orders'],
            'Average order' => App\Support\Commerce\Money::format($summary['average_order_minor'], config('store.currency', 'NGN')),
        ] as $label => $value)
            <div class="da-card p-5">
                <p class="text-xs uppercase tracking-widest text-gold-500">{{ $label }}</p>
                <p class="mt-2 font-display text-2xl text-ink">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="da-card overflow-x-auto p-6">
            <h2 class="font-display text-xl">Daily sales</h2>
            <table class="mt-5 w-full text-left text-sm">
                <thead class="border-b border-line text-xs uppercase tracking-widest text-body">
                    <tr>
                        <th class="py-2">Date</th>
                        <th class="py-2">Orders</th>
                        <th class="py-2">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daily as $day)
                        <tr class="border-b border-line last:border-0">
                            <td class="py-3">{{ $day['date'] }}</td>
                            <td class="py-3">{{ $day['orders'] }}</td>
                            <td class="py-3 font-semibold text-ink">{{ App\Support\Commerce\Money::format($day['revenue_minor'], config('store.currency', 'NGN')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-body">No sales in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="da-card overflow-x-auto p-6">
            <h2 class="font-display text-xl">Top products</h2>
            <table class="mt-5 w-full text-left text-sm">
                <thead class="border-b border-line text-xs uppercase tracking-widest text-body">
                    <tr>
                        <th class="py-2">Product</th>
                        <th class="py-2">Units</th>
                        <th class="py-2">Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topProducts as $product)
                        <tr class="border-b border-line last:border-0">
                            <td class="py-3 font-semibold text-ink">{{ $product['name'] }}</td>
                            <td class="py-3">{{ $product['units'] }}</td>
                            <td class="py-3">{{ App\Support\Commerce\Money::format($product['revenue_minor'], config('store.currency', 'NGN')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-body">No paid product sales yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
