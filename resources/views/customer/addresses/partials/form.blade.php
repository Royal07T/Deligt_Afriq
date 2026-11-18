<form method="POST" action="{{ $action }}" class="da-card mt-8 space-y-5 p-6" novalidate>
    @csrf
    @method($method)

    <div class="grid gap-5 sm:grid-cols-2">
        <x-form-input name="label" label="Address label" :value="old('label', $address->label)" />
        <x-form-input name="recipient_name" label="Recipient name" :value="old('recipient_name', $address->recipient_name)" />
        <x-form-input name="phone" label="Phone number" :value="old('phone', $address->phone)" />
        <x-form-input name="line_1" label="Address line 1" :value="old('line_1', $address->line_1)" />
        <x-form-input name="line_2" label="Address line 2 (optional)" :value="old('line_2', $address->line_2)" />
        <x-form-input name="city" label="City" :value="old('city', $address->city)" />
        <x-form-input name="state" label="State (optional)" :value="old('state', $address->state)" />
        <x-form-input name="postal_code" label="Postal code (optional)" :value="old('postal_code', $address->postal_code)" />
        <x-form-input name="country" label="Country code" :value="old('country', $address->country ?? 'NG')" />
    </div>

    <label class="flex items-center gap-3 text-sm text-body">
        <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address->is_default))>
        Use as my default address
    </label>

    <div class="flex flex-wrap gap-3">
        <x-button variant="gold">Save address</x-button>
        <a href="{{ route('customer.addresses.index') }}" class="da-btn-outline">Cancel</a>
    </div>
</form>
