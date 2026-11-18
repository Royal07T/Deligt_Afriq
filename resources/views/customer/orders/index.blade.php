<x-layouts.customer title="Orders | Delight Afriq">
    <p class="da-eyebrow">My account</p>
    <h1 class="da-heading mt-3">Orders</h1>
    <p class="mt-3 text-body">Track your Delight Afriq orders and review past purchases.</p>

    <x-alert class="mt-6" />

    @if ($orders->isEmpty())
        <div class="da-card mt-8 p-8 text-center">
            <p class="text-body">You have not placed an order yet.</p>
            <a href="{{ route('catalogue.index') }}" class="da-btn-primary mt-5">Browse the catalogue</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach ($orders as $order)
                <div class="da-card flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-gold-500">{{ $order->order_number }}</p>
                        <p class="mt-2 font-display text-xl text-ink">{{ $order->formattedTotal() }}</p>
                        <p class="mt-1 text-sm text-body">
                            {{ $order->placed_at?->format('d M Y') ?? $order->created_at->format('d M Y') }} · {{ $order->itemCount() }} item(s)
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="border border-line px-3 py-1.5 text-xs uppercase tracking-widest text-body">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                        <a href="{{ route('customer.orders.show', $order) }}" class="da-btn-outline">View order</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
</x-layouts.customer>
