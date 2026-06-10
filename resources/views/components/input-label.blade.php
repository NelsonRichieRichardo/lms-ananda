@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-bold text-[#201A17]']) }}>
    {{ $value ?? $slot }}
</label>
