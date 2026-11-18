<x-layouts.storefront title="Choose a new password | Delight Afriq" description="Set a new password for your Delight Afriq account.">
    <section class="da-container flex justify-center py-16 lg:py-24">
        <div class="da-card w-full max-w-lg p-6 sm:p-9">
            <p class="da-eyebrow">Account recovery</p>
            <h1 class="da-heading mt-3">Choose a new password</h1>
            <x-alert />
            <x-alert type="error" />
            @if ($errors->any())
                <div class="mt-4 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-form-input name="email" label="Email address" type="email" :value="$email" autocomplete="email" required />
                <x-form-input name="password" label="New password" type="password" autocomplete="new-password" required />
                <x-form-input name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" required />
                <x-button>Update password</x-button>
            </form>
        </div>
    </section>
</x-layouts.storefront>
