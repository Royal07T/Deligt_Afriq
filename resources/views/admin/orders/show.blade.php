<x-layouts.admin title="Order {{ $order->order_number }} | Delight Afriq">
    <p class="da-eyebrow">Operations</p>
    <h1 class="da-heading mt-3">{{ $order->order_number }}</h1>
    <p class="mt-3 text-body">Placed {{ $order->placed_at?->format('d M Y, H:i') ?? $order->created_at->format('d M Y, H:i') }}</p>

    <x-alert class="mt-6" />

    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="space-y-6">
            <div class="da-card p-6">
                <h2 class="font-display text-xl">Items</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between gap-4 border-b border-line pb-4 last:border-0 last:pb-0">
                            <div>
                                <p class="font-semibold text-ink">{{ $item->name }}</p>
                                <p class="mt-1 text-sm text-body">SKU {{ $item->sku }} · Quantity {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold text-navy-950">{{ $item->formattedLineTotal($order->currency) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="da-card p-6">
                <h2 class="font-display text-xl">Delivery</h2>
                <address class="mt-4 space-y-1 text-sm not-italic text-body">
                    <p class="font-semibold text-ink">{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_line_1 }}</p>
                    @if ($order->shipping_line_2)<p>{{ $order->shipping_line_2 }}</p>@endif
                    <p>{{ $order->shipping_city }}{{ $order->shipping_state ? ', '.$order->shipping_state : '' }} {{ $order->shipping_postal_code }}</p>
                    <p>{{ $order->shipping_country }}</p>
                    <p class="pt-2">{{ $order->shipping_phone }} · {{ $order->shipping_email }}</p>
                </address>
                @if ($order->notes)
                    <p class="mt-4 border-t border-line pt-4 text-sm text-body"><span class="font-semibold text-ink">Notes:</span> {{ $order->notes }}</p>
                @endif
            </div>
        </div>

        <aside class="space-y-6">
            <div class="da-card p-6">
                <h2 class="font-display text-xl">Summary</h2>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-body">Customer</dt>
                        <dd class="font-semibold text-ink">{{ $order->user?->name ?? 'Guest' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-body">Payment method</dt>
                        <dd class="font-semibold text-ink">{{ $order->payment_method }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-body">Payment status</dt>
                        <dd class="font-semibold text-ink">{{ $order->payment_status }}</dd>
                    </div>
                    @if ($order->payment_reference)
                        <div class="flex justify-between gap-4">
                            <dt class="text-body">Payment reference</dt>
                            <dd class="break-all text-right font-semibold text-ink">{{ $order->payment_reference }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 border-t border-line pt-3 text-base">
                        <dt class="font-semibold text-ink">Total</dt>
                        <dd class="font-display text-navy-950">{{ $order->formattedTotal() }}</dd>
                    </div>
                </dl>
            </div>

            @can('update', $order)
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="da-card space-y-4 p-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="status" class="da-label">Order status</label>
                        <select id="status" name="status" class="da-input">
                            @foreach (App\Support\Orders\OrderStatus::ALL as $status)
                                <option value="{{ $status }}" @selected($order->status === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                    <x-button variant="gold">Update status</x-button>
                </form>
            @endcan
        </aside>
    </div>
</x-layouts.admin>
