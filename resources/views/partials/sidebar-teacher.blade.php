@php
    $navClass = function (bool $active) {
        return ($active
            ? 'bg-white text-[#5B1010] shadow-sm'
            : 'text-white/76 hover:bg-white/10 hover:text-white')
            .' flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition';
    };
@endphp

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Teaching') }}</p>
    <div class="space-y-1">
        <a href="{{ route('teacher.dashboard') }}" class="{{ $navClass(request()->routeIs('teacher.dashboard')) }}"><span class="h-2 w-2 rounded-full {{ request()->routeIs('teacher.dashboard') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Overview') }}</a>
        <a href="{{ route('teacher.courses.index') }}" class="{{ $navClass(request()->routeIs('teacher.courses.*')) }}"><span class="h-2 w-2 rounded-full {{ request()->routeIs('teacher.courses.*') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('My courses') }}</a>
        <a href="{{ route('teacher.gradebook.index') }}" class="{{ $navClass(request()->routeIs('teacher.gradebook.*')) }}"><span class="h-2 w-2 rounded-full {{ request()->routeIs('teacher.gradebook.*') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Gradebook') }}</a>
    </div>
</div>

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Account') }}</p>
    <div class="space-y-1">
        @include('partials.sidebar-account-link')
    </div>
</div>
