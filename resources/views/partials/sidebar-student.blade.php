@php
    $navClass = function (bool $active) {
        return ($active
            ? 'bg-white text-[#5B1010] shadow-sm'
            : 'text-white/76 hover:bg-white/10 hover:text-white')
            .' flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition';
    };
@endphp

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Academic') }}</p>
    <div class="space-y-1">
        <a href="{{ route('student.dashboard') }}" class="{{ $navClass(request()->routeIs('student.dashboard')) }}">
            <span class="h-2 w-2 rounded-full {{ request()->routeIs('student.dashboard') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Dashboard') }}
        </a>
        <a href="{{ route('student.courses.index') }}" class="{{ $navClass(request()->routeIs('student.courses.*')) }}">
            <span class="h-2 w-2 rounded-full {{ request()->routeIs('student.courses.*') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Courses') }}
        </a>
        <a href="#" class="{{ $navClass(false) }}"><span class="h-2 w-2 rounded-full bg-white/25"></span>{{ __('Assignments') }}</a>
        <a href="#" class="{{ $navClass(false) }}"><span class="h-2 w-2 rounded-full bg-white/25"></span>{{ __('Schedule') }}</a>
    </div>
</div>

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Progress') }}</p>
    <div class="space-y-1">
        <a href="#" class="{{ $navClass(false) }}"><span class="h-2 w-2 rounded-full bg-white/25"></span>{{ __('Grades') }}</a>
        <a href="#" class="{{ $navClass(false) }}"><span class="h-2 w-2 rounded-full bg-white/25"></span>{{ __('Resources') }}</a>
    </div>
</div>

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Account') }}</p>
    <div class="space-y-1">
        @include('partials.sidebar-account-link')
    </div>
</div>
