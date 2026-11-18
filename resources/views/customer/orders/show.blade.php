<x-layouts.customer title="Order {{ $order->order_number }} | Delight Afriq">
    <p class="da-eyebrow">Order details</p>
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
                                <p class="mt-1 text-sm text-body">SKU {{ $item->sku }} · {{ $item->quantity }} × {{ $item->formattedLineTotal($order->currency) }}</p>
                            </div>
                            <p class="font-semibold text-navy-950">{{ $item->formattedLineTotal($order->currency) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="da-card p-6">
                <h2 class="font-display text-xl">Delivery address</h2>
                <address class="mt-4 space-y-1 text-sm not-italic text-body">
                    <p class="font-semibold text-ink">{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_line_1 }}</p>
                    @if ($order->shipping_line_2)<p>{{ $order->shipping_line_2 }}</p>@endif
                    <p>{{ $order->shipping_city }}{{ $order->shipping_state ? ', '.$order->shipping_state : '' }} {{ $order->shipping_postal_code }}</p>
                    <p>{{ $order->shipping_country }}</p>
                    <p class="pt-2">{{ $order->shipping_phone }} · {{ $order->shipping_email }}</p>
                </address>
            </div>
        </div>

        <aside class="da-card h-fit p-6">
            <h2 class="font-display text-xl">Summary</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-body">Status</dt>
                    <dd class="font-semibold text-ink">{{ str_replace('_', ' ', ucfirst($order->status)) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-body">Payment</dt>
                    <dd class="font-semibold text-ink">{{ str_replace('_', ' ', ucfirst($order->payment_status)) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-body">Subtotal</dt>
                    <dd class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($order->subtotalMinor(), $order->currency) }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-body">Delivery</dt>
                    <dd class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($order->shippingMinor(), $order->currency) }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-line pt-3 text-base">
                    <dt class="font-semibold text-ink">Total</dt>
                    <dd class="font-display text-navy-950">{{ $order->formattedTotal() }}</dd>
                </div>
            </dl>

            @if ($order->payment_method === App\Support\Orders\PaymentMethod::WHATSAPP && $order->payment_status !== App\Support\Orders\PaymentStatus::PAID)
                <a href="{{ App\Support\Orders\WhatsAppHandoff::link($order) }}" target="_blank" rel="noreferrer" class="da-btn-gold mt-6 w-full text-center">Pay on WhatsApp</a>
            @endif

            @if ($order->canBeCancelledByCustomer())
                <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="mt-4">
                    @csrf
                    <button type="submit" class="text-sm text-red-700 underline">Cancel this order</button>
                </form>
            @endif
        </aside>
    </div>
</x-layouts.customer>
