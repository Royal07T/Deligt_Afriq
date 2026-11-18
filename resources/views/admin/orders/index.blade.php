<x-layouts.admin title="Orders | Delight Afriq">
    <p class="da-eyebrow">Operations</p>
    <h1 class="da-heading mt-3">Orders</h1>

    <x-alert class="mt-6" />

    <form method="GET" action="{{ route('admin.orders.index') }}" class="da-card mt-8 grid gap-4 p-5 sm:grid-cols-3">
        <div>
            <label for="status" class="da-label">Order status</label>
            <select id="status" name="status" class="da-input">
                <option value="">All</option>
                @foreach (App\Support\Orders\OrderStatus::ALL as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="payment_status" class="da-label">Payment status</label>
            <select id="payment_status" name="payment_status" class="da-input">
                <option value="">All</option>
                @foreach (App\Support\Orders\PaymentStatus::ALL as $status)
                    <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <x-button variant="outline">Filter</x-button>
        </div>
    </form>

    <div class="da-card mt-6 overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-line bg-cream text-xs uppercase tracking-widest text-body">
                <tr>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Payment</th>
                    <th class="px-5 py-3">Total</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b border-line last:border-0">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-ink">{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-body">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-5 py-4 text-body">{{ $order->user?->name ?? 'Guest' }}</td>
                        <td class="px-5 py-4 text-body">{{ str_replace('_', ' ', $order->status) }}</td>
                        <td class="px-5 py-4 text-body">{{ str_replace('_', ' ', $order->payment_status) }}</td>
                        <td class="px-5 py-4 font-semibold text-ink">{{ $order->formattedTotal() }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-navy-950 underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-body">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</x-layouts.admin>
