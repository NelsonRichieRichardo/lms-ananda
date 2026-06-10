@props([
    'type' => 'info',
])

@php
    $styles = match ($type) {
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'danger' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        default => 'border-[#6B1212]/20 bg-[#6B1212]/5 text-[#6B1212]',
    };
@endphp

<div {{ $attributes->merge(['class' => 'mb-4 rounded border px-4 py-3 text-sm '.$styles]) }} role="alert">
    {{ $slot }}
</div>
