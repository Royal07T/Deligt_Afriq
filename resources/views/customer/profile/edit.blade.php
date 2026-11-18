<x-layouts.customer title="Profile | Delight Afriq">
    <p class="da-eyebrow">My account</p>
    <h1 class="da-heading mt-3">Profile &amp; preferences</h1>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="da-card p-6 sm:p-8">
            <h2 class="font-display text-xl">Personal details</h2>
            <form method="POST" action="{{ route('customer.profile.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')
                <x-form-input name="name" label="Full name" :value="$user->name" required />
                <x-form-input name="email" label="Email address" type="email" :value="$user->email" required />
                <x-form-input name="phone" label="Phone number" type="tel" :value="$user->phone" />
                <label class="flex items-start gap-2 text-sm text-body">
                    <input type="hidden" name="marketing_opt_in" value="0">
                    <input type="checkbox" name="marketing_opt_in" value="1" @checked(old('marketing_opt_in', $user->marketing_opt_in)) class="mt-1 accent-navy-950">
                    <span>Send me collection news and offers.</span>
                </label>
                <x-button>Save changes</x-button>
            </form>
        </div>

        <div class="da-card h-fit p-6 sm:p-8">
            <h2 class="font-display text-xl">Change password</h2>
            <form method="POST" action="{{ route('customer.password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')
                <x-form-input name="current_password" label="Current password" type="password" autocomplete="current-password" required />
                <x-form-input name="password" label="New password" type="password" autocomplete="new-password" required />
                <x-form-input name="password_confirmation" label="Confirm new password" type="password" autocomplete="new-password" required />
                <x-button variant="outline">Update password</x-button>
            </form>
        </div>
    </div>
</x-layouts.customer>
