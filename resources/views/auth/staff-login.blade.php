<x-layouts.storefront title="Administration sign in | Delight Afriq" description="Secure sign in for Delight Afriq staff.">
    <section class="da-container flex justify-center py-16 lg:py-24">
        <div class="da-card w-full max-w-lg p-6 sm:p-9">
            <p class="da-eyebrow">Staff access</p>
            <h1 class="da-heading mt-3">Administration sign in</h1>
            <p class="mt-3 text-sm text-body">This portal is restricted to authorized Delight Afriq team members.</p>

            <div class="mt-6 space-y-4">
                <x-alert />
                <x-alert type="error" />
                @if ($errors->any())
                    <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-5">
                @csrf
                <x-form-input name="email" label="Work email" type="email" autocomplete="email" required />
                <x-form-input name="password" label="Password" type="password" autocomplete="current-password" required />
                <label class="flex items-center gap-2 text-sm text-body">
                    <input type="checkbox" name="remember" value="1" class="accent-navy-950">
                    Remember me
                </label>
                <x-button class="w-full">Sign in securely</x-button>
            </form>
            <p class="mt-5 text-sm"><a href="{{ route('home') }}" class="text-navy-950 underline decoration-gold-500">Return to storefront</a></p>
        </div>
    </section>
</x-layouts.storefront>
