<x-layouts.storefront title="Verify your email | Delight Afriq" description="Verify the email address linked to your Delight Afriq account.">
    <section class="da-container flex justify-center py-16 lg:py-24">
        <div class="da-card w-full max-w-xl p-6 sm:p-9">
            <p class="da-eyebrow">One more step</p>
            <h1 class="da-heading mt-3">Verify your email address</h1>
            <x-alert />
            <x-alert type="error" />
            <p class="mt-4 text-body">Please check your inbox for the verification link we sent to <span class="font-semibold text-ink">{{ auth()->user()->email }}</span>.</p>
            <form method="POST" action="{{ route('verification.send') }}" class="mt-7">
                @csrf
                <x-button variant="outline">Resend verification email</x-button>
            </form>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm text-navy-950 underline decoration-gold-500">Sign out</button>
            </form>
        </div>
    </section>
</x-layouts.storefront>
