<x-layouts.customer title="Edit address | Delight Afriq">
    <p class="da-eyebrow">My account</p>
    <h1 class="da-heading mt-3">Edit address</h1>
    @include('customer.addresses.partials.form', [
        'address' => $address,
        'action' => route('customer.addresses.update', $address),
        'method' => 'PUT',
    ])
</x-layouts.customer>
