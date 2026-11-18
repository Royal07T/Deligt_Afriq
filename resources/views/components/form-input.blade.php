@props(['name', 'label', 'type' => 'text', 'value' => null, 'autocomplete' => null, 'required' => false])

<div @if ($type === 'password') x-data="{ visible: false }" @endif>
    <label for="{{ $name }}" class="da-label">{{ $label }}</label>
    <div class="relative">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($type === 'password') x-bind:type="visible ? 'text' : 'password'" @endif
            value="{{ old($name, $value) }}"
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if ($required) required @endif
            {{ $attributes->merge(['class' => 'da-input'.($type === 'password' ? ' pr-12' : '')]) }}
        >
        @if ($type === 'password')
            <button
                type="button"
                aria-label="Toggle password visibility"
                :aria-pressed="visible.toString()"
                @click="visible = ! visible"
                class="absolute inset-y-0 right-0 flex items-center pr-3 text-body transition hover:text-navy-950"
            >
                <svg x-show="! visible" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75 9.75 6.75 9.75 6.75-3.75 6.75-9.75 6.75S2.25 12 2.25 12Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <svg x-show="visible" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.25 12s3.75 6.75 9.75 6.75c1.677 0 3.262-.392 4.672-1.075M6.228 6.228A10.45 10.45 0 0 1 2.25 12s3.75 6.75 9.75 6.75c.612 0 1.203-.063 1.772-.18M10.6 10.6a3 3 0 0 0 4.2 4.2" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
                </svg>
            </button>
        @endif
    </div>
    @error($name)
        <p class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
    @enderror
</div>
