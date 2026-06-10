<button {{ $attributes->merge(['type' => 'submit', 'class' => 'portal-button-primary disabled:opacity-50']) }}>
    {{ $slot }}
</button>
