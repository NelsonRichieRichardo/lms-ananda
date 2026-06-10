<button {{ $attributes->merge(['type' => 'button', 'class' => 'portal-button-secondary disabled:opacity-50']) }}>
    {{ $slot }}
</button>
