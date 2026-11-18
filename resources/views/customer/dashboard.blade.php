<x-layouts.customer title="Account overview | Delight Afriq">
    <p class="da-eyebrow">My account</p>
    <h1 class="da-heading mt-3">Welcome, {{ auth()->user()->name }}</h1>
    <p class="mt-3 text-body">Your Delight Afriq account is active. Email verification: <span class="font-semibold text-ink">{{ auth()->user()->hasVerifiedEmail() ? 'Complete' : 'Pending' }}</span>.</p>

    <div class="mt-8 grid gap-5 sm:grid-cols-2">
        <div class="da-card p-6">
            <p class="text-xs uppercase tracking-widest text-gold-500">Member since</p>
            <p class="mt-2 font-display text-xl text-ink">{{ auth()->user()->created_at->format('F Y') }}</p>
        </div>
        <div class="da-card p-6">
            <p class="text-xs uppercase tracking-widest text-gold-500">Communication</p>
            <p class="mt-2 text-sm text-body">{{ auth()->user()->marketing_opt_in ? 'Collection updates enabled' : 'Order notifications only' }}</p>
        </div>
    </div>

    <div class="mt-8 da-card p-6">
        <h2 class="font-display text-xl">Quick links</h2>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('customer.orders.index') }}" class="da-btn-primary">View orders</a>
            <a href="{{ route('customer.addresses.index') }}" class="da-btn-outline">Saved addresses</a>
            <a href="{{ route('customer.profile.edit') }}" class="da-btn-outline">Manage profile</a>
            <a href="{{ config('brand.whatsapp_url') }}" target="_blank" rel="noreferrer" class="da-btn-outline">Contact stylist</a>
        </div>
    </div>
</x-layouts.customer>
