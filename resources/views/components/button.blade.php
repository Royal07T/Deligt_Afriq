@props(['variant' => 'primary', 'type' => 'submit'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => $variant === 'gold' ? 'da-btn-gold' : ($variant === 'outline' ? 'da-btn-outline' : 'da-btn-primary')]) }}>
    {{ $slot }}
</button>
