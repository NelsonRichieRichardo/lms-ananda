@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'portal-input']) }}>
