<x-layouts.customer title="Add address | Delight Afriq">
    <p class="da-eyebrow">My account</p>
    <h1 class="da-heading mt-3">Add an address</h1>
    @include('customer.addresses.partials.form', [
        'address' => $address,
        'action' => route('customer.addresses.store'),
        'method' => 'POST',
    ])
</x-layouts.customer>
