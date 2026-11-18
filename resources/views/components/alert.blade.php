@props(['type' => 'status'])

@php
    $message = $type === 'error' ? session('error') : session('status');
@endphp

@if ($message)
    <div {{ $attributes->merge(['class' => 'border px-4 py-3 text-sm ' . ($type === 'error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800')]) }} role="alert">
        {{ $message }}
    </div>
@endif
