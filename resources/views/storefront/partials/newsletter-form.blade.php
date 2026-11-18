<div>
    <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-gold-500">Newsletter</p>
    <p class="mb-4 text-sm text-gold-100/70">Collection launches, styling notes and exclusive updates.</p>
    <form method="POST" action="{{ route('newsletter.subscribe') }}" class="flex flex-col gap-2">
        @csrf
        <label for="newsletter-email" class="sr-only">Email address</label>
        <input id="newsletter-email" name="email" type="email" autocomplete="email" placeholder="you@example.com" required class="w-full rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm text-white placeholder:text-gold-100/40">
        <label for="newsletter-name" class="sr-only">Name</label>
        <input id="newsletter-name" name="name" type="text" autocomplete="name" placeholder="Your name (optional)" class="w-full rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm text-white placeholder:text-gold-100/40">
        <x-button type="submit" variant="gold" class="w-full">Subscribe</x-button>
    </form>
    @error('email')
        <p class="mt-2 text-sm text-red-300" role="alert">{{ $message }}</p>
    @enderror
</div>
