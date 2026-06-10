@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'portal-card rounded-[1.5rem] p-6']) }}>
    @if ($title)
        <div class="mb-5 flex items-center justify-between gap-4">
            <h3 class="portal-heading text-lg font-bold text-[#201A17]">{{ $title }}</h3>
        </div>
    @endif
    {{ $slot }}
</div>
