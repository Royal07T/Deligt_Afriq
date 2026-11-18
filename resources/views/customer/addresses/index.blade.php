<x-layouts.customer title="Addresses | Delight Afriq">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="da-eyebrow">My account</p>
            <h1 class="da-heading mt-3">Saved addresses</h1>
        </div>
        <a href="{{ route('customer.addresses.create') }}" class="da-btn-primary">Add address</a>
    </div>

    <x-alert class="mt-6" />

    @if ($addresses->isEmpty())
        <div class="da-card mt-8 p-8 text-center">
            <p class="text-body">You have not saved any addresses yet.</p>
        </div>
    @else
        <div class="mt-8 grid gap-5 md:grid-cols-2">
            @foreach ($addresses as $address)
                <div class="da-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-display text-xl text-ink">{{ $address->label }}</p>
                            @if ($address->is_default)
                                <p class="mt-1 text-xs uppercase tracking-widest text-gold-500">Default</p>
                            @endif
                        </div>
                        @if (! $address->is_default)
                            <form method="POST" action="{{ route('customer.addresses.default', $address) }}">
                                @csrf
                                <button type="submit" class="text-sm text-body underline">Make default</button>
                            </form>
                        @endif
                    </div>
                    <address class="mt-4 space-y-1 text-sm not-italic text-body">
                        <p class="font-semibold text-ink">{{ $address->recipient_name }}</p>
                        <p>{{ $address->fullAddress() }}</p>
                        <p class="pt-2">{{ $address->phone }}</p>
                    </address>
                    <div class="mt-5 flex flex-wrap gap-4">
                        <a href="{{ route('customer.addresses.edit', $address) }}" class="text-sm text-navy-950 underline">Edit</a>
                        <form method="POST" action="{{ route('customer.addresses.destroy', $address) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-700 underline">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.customer>
