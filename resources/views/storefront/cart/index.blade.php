<x-layouts.storefront :title="'Your bag | '.config('brand.name')">
    <section class="da-container py-12">
        <p class="da-eyebrow">Shopping bag</p>
        <h1 class="da-heading mt-3">Your bag</h1>

        <x-alert class="mt-6" />
        <x-alert type="error" class="mt-2" />

        @if ($cart->isEmpty())
            <div class="da-card mt-8 p-8 text-center">
                <p class="text-body">Your bag is empty.</p>
                <a href="{{ route('catalogue.index') }}" class="da-btn-primary mt-5">Browse the catalogue</a>
            </div>
        @else
            @php $currency = config('store.currency', 'NGN'); @endphp
            <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_340px]">
                <div class="space-y-4">
                    @foreach ($cart->items as $item)
                        <div class="da-card flex flex-col gap-5 p-5 sm:flex-row sm:items-center">
                            <img
                                src="{{ $item->product?->primaryImage?->url ?? asset('media/images/Allin1.jpeg') }}"
                                alt="{{ $item->product?->name ?? 'Product' }}"
                                class="h-28 w-24 shrink-0 object-cover"
                            >
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('products.show', $item->product) }}" class="font-display text-xl text-ink hover:text-gold-500">
                                    {{ $item->product?->name ?? 'Unavailable product' }}
                                </a>
                                <p class="mt-1 text-sm text-body">SKU {{ $item->product?->sku }}</p>
                                <p class="mt-2 font-semibold text-navy-950">
                                    {{ App\Support\Commerce\Money::format($item->lineTotalMinor(), $currency) }}
                                </p>

                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    <form method="POST" action="{{ route('cart.update', $item->product) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <label class="sr-only" for="quantity-{{ $item->id }}">Quantity</label>
                                        <input id="quantity-{{ $item->id }}" name="quantity" type="number" min="1" value="{{ $item->quantity }}" class="da-input !w-20">
                                        <x-button variant="outline">Update</x-button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item->product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-700 underline">Remove</button>
                                    </form>
                                </div>
                                @error('quantity')
                                    <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="da-card h-fit p-6">
                    <h2 class="font-display text-xl">Order summary</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-body">Items ({{ $cart->itemCount() }})</dt>
                            <dd class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($subtotalMinor, $currency) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-body">Estimated delivery</dt>
                            <dd class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($shippingMinor, $currency) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 border-t border-line pt-3 text-base">
                            <dt class="font-semibold text-ink">Total</dt>
                            <dd class="font-display text-navy-950">{{ App\Support\Commerce\Money::format($totalMinor, $currency) }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('checkout.index') }}" class="da-btn-gold mt-6 w-full text-center">Proceed to checkout</a>
                    <p class="mt-3 text-xs text-body">Checkout requires a verified Delight Afriq account.</p>
                </aside>
            </div>
        @endif
    </section>
</x-layouts.storefront>
