<x-layouts.storefront title="Reset password | Delight Afriq" description="Request a password reset link for your Delight Afriq account.">
    <section class="da-container flex justify-center py-16 lg:py-24">
        <div class="da-card w-full max-w-lg p-6 sm:p-9">
            <p class="da-eyebrow">Account recovery</p>
            <h1 class="da-heading mt-3">Forgot your password?</h1>
            <p class="mt-3 text-sm text-body">Enter your email address and we will send you a secure reset link.</p>
            <x-alert />
            <x-alert type="error" />
            @if ($errors->any())
                <div class="mt-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                @csrf
                <x-form-input name="email" label="Email address" type="email" autocomplete="email" required />
                <x-button>Send reset link</x-button>
            </form>
            <p class="mt-5 text-sm"><a href="{{ route('login') }}" class="text-navy-950 underline decoration-gold-500">Back to sign in</a></p>
        </div>
    </section>
</x-layouts.storefront>
