<x-layouts.storefront :title="'Checkout | '.config('brand.name')" description="Complete your Delight Afriq order.">
    <section class="da-container py-12">
        <p class="da-eyebrow">Checkout</p>
        <h1 class="da-heading mt-3">Delivery and payment</h1>
        <p class="mt-3 text-body">Review your details and place your order. Payments are coordinated through our WhatsApp concierge while Paystack integration is completed.</p>

        <x-alert class="mt-6" />
        <x-alert type="error" class="mt-2" />

        @php $currency = config('store.currency', 'NGN'); @endphp
        <form method="POST" action="{{ route('checkout.store') }}" class="mt-8 grid gap-8 lg:grid-cols-[1fr_340px]" novalidate>
            @csrf
            <div class="space-y-6">
                <div class="da-card p-6">
                    <h2 class="font-display text-xl">Delivery details</h2>

                    @if ($addresses->isNotEmpty())
                        <div class="mt-5">
                            <label for="address_id" class="da-label">Use a saved address</label>
                            <select id="address_id" name="address_id" class="da-input">
                                <option value="">Enter a new address</option>
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" @selected(old('address_id') == $address->id)>
                                        {{ $address->label }} — {{ $address->fullAddress() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('address_id')
                                <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <x-form-input name="shipping_name" label="Full name" :value="old('shipping_name', auth()->user()->name)" />
                        <x-form-input name="shipping_phone" label="Phone number" :value="old('shipping_phone', auth()->user()->phone)" />
                        <x-form-input name="shipping_email" label="Email address" type="email" :value="old('shipping_email', auth()->user()->email)" />
                        <x-form-input name="shipping_line_1" label="Address line 1" :value="old('shipping_line_1')" />
                        <x-form-input name="shipping_line_2" label="Address line 2 (optional)" :value="old('shipping_line_2')" />
                        <x-form-input name="shipping_city" label="City" :value="old('shipping_city')" />
                        <x-form-input name="shipping_state" label="State (optional)" :value="old('shipping_state')" />
                        <x-form-input name="shipping_postal_code" label="Postal code (optional)" :value="old('shipping_postal_code')" />
                        <x-form-input name="shipping_country" label="Country code" :value="old('shipping_country', 'NG')" />
                    </div>
                </div>

                <div class="da-card p-6">
                    <h2 class="font-display text-xl">Payment method</h2>
                    <div class="mt-5 space-y-3">
                        @foreach ($paymentMethods as $method)
                            <label class="flex items-start gap-3 border border-line p-4">
                                <input type="radio" name="payment_method" value="{{ $method }}" class="mt-1" @checked(old('payment_method', App\Support\Orders\PaymentMethod::WHATSAPP) === $method)>
                                <span>
                                    <span class="block font-semibold text-ink">{{ App\Support\Orders\PaymentMethod::LABELS[$method] }}</span>
                                    @if ($method === App\Support\Orders\PaymentMethod::WHATSAPP)
                                        <span class="mt-1 block text-sm text-body">We will send a WhatsApp payment link and confirm your order.</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach

                        @if (! in_array(App\Support\Orders\PaymentMethod::PAYSTACK, $paymentMethods, true))
                            <div class="border border-dashed border-line p-4 text-sm text-body">
                                Paystack card payments arrive in the next phase. Select WhatsApp concierge for now.
                            </div>
                        @endif
                        @error('payment_method')
                            <p class="text-sm text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="notes" class="da-label">Delivery notes (optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="da-input">{{ old('notes') }}</textarea>
                    </div>

                    <label class="mt-6 flex items-start gap-3 text-sm text-body">
                        <input type="checkbox" name="terms" value="1" class="mt-1" @checked(old('terms'))>
                        <span>I confirm the delivery details and understand that payment is coordinated after checkout.</span>
                    </label>
                    @error('terms')
                        <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <aside class="da-card h-fit p-6">
                <h2 class="font-display text-xl">Your order</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($cart->items as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <span class="text-body">{{ $item->quantity }} × {{ $item->product?->name }}</span>
                            <span class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($item->lineTotalMinor(), $currency) }}</span>
                        </div>
                    @endforeach
                </div>
                <dl class="mt-6 space-y-3 border-t border-line pt-5 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-body">Subtotal</dt>
                        <dd class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($subtotalMinor, $currency) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-body">Delivery</dt>
                        <dd class="font-semibold text-ink">{{ App\Support\Commerce\Money::format($shippingMinor, $currency) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-t border-line pt-3 text-base">
                        <dt class="font-semibold text-ink">Total</dt>
                        <dd class="font-display text-navy-950">{{ App\Support\Commerce\Money::format($totalMinor, $currency) }}</dd>
                    </div>
                </dl>
                <x-button variant="gold" class="mt-6 w-full">Place order</x-button>
                <a href="{{ route('cart.index') }}" class="mt-4 block text-center text-sm text-body underline">Return to bag</a>
            </aside>
        </form>
    </section>
</x-layouts.storefront>
