<a
    href="{{ route('profile.edit') }}"
    class="{{ request()->routeIs('profile.edit') ? 'bg-white text-[#5B1010] shadow-sm' : 'text-white/76 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition"
>
    <span class="h-2 w-2 rounded-full {{ request()->routeIs('profile.edit') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Profile') }}
</a>
