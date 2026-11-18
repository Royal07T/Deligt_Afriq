<x-layouts.storefront title="Create an account | Delight Afriq" description="Join Delight Afriq for a refined shopping experience and access to bespoke fashion services.">
    <section class="da-container grid gap-12 py-16 lg:grid-cols-2 lg:py-24">
        <div>
            <p class="da-eyebrow">Membership</p>
            <h1 class="da-heading mt-4">Join Delight Afriq</h1>
            <p class="mt-4 max-w-md text-body">Create an account to manage orders, save delivery addresses and receive updates on new collections. A verified account is required to check out.</p>
            <img src="{{ asset('media/images/Family.png') }}" alt="Delight Afriq editorial" class="mt-8 hidden w-full max-w-md object-cover lg:block" loading="lazy">
        </div>

        <div class="da-card p-6 sm:p-9">
            <x-alert />
            <x-alert type="error" />
            @if ($errors->any())
                <div class="mb-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                <x-form-input name="name" label="Full name" autocomplete="name" required />
                <x-form-input name="email" label="Email address" type="email" autocomplete="email" required />
                <x-form-input name="phone" label="Phone number" type="tel" autocomplete="tel" />
                <x-form-input name="password" label="Password" type="password" autocomplete="new-password" required />
                <x-form-input name="password_confirmation" label="Confirm password" type="password" autocomplete="new-password" required />
                <div class="space-y-2 text-sm text-body">
                    <label class="flex items-start gap-2">
                        <input type="hidden" name="communication_preferences[email]" value="0">
                        <input type="checkbox" name="communication_preferences[email]" value="1" checked class="mt-1 accent-navy-950">
                        <span>Email me order updates and account notifications.</span>
                    </label>
                    <label class="flex items-start gap-2">
                        <input type="hidden" name="marketing_opt_in" value="0">
                        <input type="checkbox" name="marketing_opt_in" value="1" class="mt-1 accent-navy-950">
                        <span>Send me occasional collection news and offers.</span>
                    </label>
                </div>
                <x-button variant="gold" class="w-full">Create account</x-button>
            </form>
            <p class="mt-5 text-sm text-body">Already a member? <a href="{{ route('login') }}" class="font-semibold text-navy-950 underline decoration-gold-500">Sign in</a></p>
        </div>
    </section>
</x-layouts.storefront>
