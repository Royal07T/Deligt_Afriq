<x-layouts.storefront :title="'Order '.$order->order_number.' | '.config('brand.name')">
    <section class="da-container py-12">
        <p class="da-eyebrow">Order confirmation</p>
        <h1 class="da-heading mt-3">Thank you for your order</h1>
        <p class="mt-3 text-body">Your order <span class="font-semibold text-ink">{{ $order->order_number }}</span> has been placed. Keep this reference for your records.</p>

        <x-alert class="mt-6" />

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_340px]">
            <div class="da-card p-6">
                <dl class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-body">Order number</dt>
                        <dd class="mt-1 font-semibold text-ink">{{ $order->order_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-body">Status</dt>
                        <dd class="mt-1 font-semibold text-ink">{{ str_replace('_', ' ', ucfirst($order->status)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-body">Payment method</dt>
                        <dd class="mt-1 font-semibold text-ink">{{ App\Support\Orders\PaymentMethod::LABELS[$order->payment_method] ?? $order->payment_method }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-widest text-body">Total</dt>
                        <dd class="mt-1 font-display text-xl text-navy-950">{{ $order->formattedTotal() }}</dd>
                    </div>
                </dl>

                <div class="mt-8 border-t border-line pt-6">
                    @if ($order->payment_method === App\Support\Orders\PaymentMethod::WHATSAPP)
                        <h2 class="font-display text-xl">Complete payment on WhatsApp</h2>
                        <p class="mt-2 text-body">Open WhatsApp to send your order reference to our concierge. We will confirm payment and delivery details.</p>
                        <a href="{{ $whatsappLink }}" target="_blank" rel="noreferrer" class="da-btn-gold mt-5">Continue on WhatsApp</a>
                    @else
                        <h2 class="font-display text-xl">Paystack payment</h2>
                        @if ($isPaid)
                            <p class="mt-2 text-body">Payment received. Your order is now being prepared.</p>
                        @else
                            <p class="mt-2 text-body">If you were interrupted before completing checkout, continue securely with Paystack.</p>
                            <p class="mt-3 text-sm text-body">Payment reference: <span class="font-semibold text-ink">{{ $order->payment_reference }}</span></p>
                            @if ($transactions->isNotEmpty())
                                <ul class="mt-4 space-y-2 text-sm">
                                    @foreach ($transactions as $transaction)
                                        <li class="flex justify-between gap-4 border-b border-line pb-2">
                                            <span class="text-body">{{ $transaction->reference }}</span>
                                            <span class="font-semibold text-ink">{{ $transaction->status }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <form method="POST" action="{{ route('checkout.paystack.retry', $order) }}" class="mt-5">
                                @csrf
                                <x-button variant="gold">Continue to Paystack</x-button>
                            </form>
                        @endif
                    @endif
                </div>

                <div class="mt-8 border-t border-line pt-6">
                    <h2 class="font-display text-xl">Items</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between gap-4 text-sm">
                                <span class="text-body">{{ $item->quantity }} × {{ $item->name }}</span>
                                <span class="font-semibold text-ink">{{ $item->formattedLineTotal($order->currency) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <aside class="da-card h-fit p-6">
                <h2 class="font-display text-xl">Next steps</h2>
                <p class="mt-3 text-sm text-body">Track this order and view its full delivery details in your customer portal.</p>
                <a href="{{ route('customer.orders.show', $order) }}" class="da-btn-primary mt-5 w-full text-center">View order</a>
                <a href="{{ route('catalogue.index') }}" class="mt-4 block text-center text-sm text-body underline">Continue shopping</a>
            </aside>
        </div>
    </section>
</x-layouts.storefront>
