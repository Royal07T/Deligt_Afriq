<x-layouts.storefront title="Sign in | Delight Afriq" description="Sign in to your Delight Afriq account to track orders and manage your profile.">
    <section class="da-container grid gap-12 py-16 lg:grid-cols-2 lg:py-24">
        <div>
            <p class="da-eyebrow">Welcome back</p>
            <h1 class="da-heading mt-4">Sign in to your account</h1>
            <p class="mt-4 max-w-md text-body">Access your Delight Afriq orders, saved details and bespoke requests.</p>
            <div class="mt-8 border-t border-line pt-6 text-sm text-body">
                <p>New to Delight Afriq?</p>
                <a href="{{ route('register') }}" class="mt-2 inline-block font-semibold text-navy-950 underline decoration-gold-500">Create an account</a>
                <p class="mt-4">Staff member? <a href="{{ route('admin.login') }}" class="font-semibold text-navy-950 underline decoration-gold-500">Administration sign in</a></p>
            </div>
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

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <x-form-input name="email" label="Email address" type="email" autocomplete="email" required />
                <x-form-input name="password" label="Password" type="password" autocomplete="current-password" required />
                <label class="flex items-center gap-2 text-sm text-body">
                    <input type="checkbox" name="remember" value="1" class="accent-navy-950">
                    Remember me
                </label>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <x-button>Sign in</x-button>
                    <a href="{{ route('password.request') }}" class="text-sm text-navy-950 underline decoration-gold-500">Forgot your password?</a>
                </div>
            </form>
        </div>
    </section>
</x-layouts.storefront>
