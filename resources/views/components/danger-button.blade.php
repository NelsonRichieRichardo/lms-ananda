<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded border border-[#6B1212] bg-[#6B1212] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#5A1010] focus:outline-none focus:ring-2 focus:ring-[#6B1212]/20 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
