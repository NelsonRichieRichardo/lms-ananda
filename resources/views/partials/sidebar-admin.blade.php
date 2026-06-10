@php
    $studentsNavActive = request()->routeIs('admin.students.*') && ! request()->routeIs('admin.students.import*');
    $teachersNavActive = request()->routeIs('admin.teachers.*');
    $importNavActive = request()->routeIs('admin.students.import*');
    $navClass = function (bool $active) {
        return ($active
            ? 'bg-white text-[#5B1010] shadow-sm'
            : 'text-white/76 hover:bg-white/10 hover:text-white')
            .' flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold transition';
    };
@endphp

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Administration') }}</p>
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="{{ $navClass(request()->routeIs('admin.dashboard')) }}"><span class="h-2 w-2 rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Overview') }}</a>
        <a href="{{ route('admin.students.index') }}" class="{{ $navClass($studentsNavActive) }}"><span class="h-2 w-2 rounded-full {{ $studentsNavActive ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Students') }}</a>
        <a href="{{ route('admin.teachers.index') }}" class="{{ $navClass($teachersNavActive) }}"><span class="h-2 w-2 rounded-full {{ $teachersNavActive ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Teachers') }}</a>
        <a href="{{ route('admin.students.import') }}" class="{{ $navClass($importNavActive) }}"><span class="h-2 w-2 rounded-full {{ $importNavActive ? 'bg-[#6B1212]' : 'bg-white/25' }}"></span>{{ __('Import students') }}</a>
    </div>
</div>

<div>
    <p class="mb-2 px-3 text-[0.68rem] font-bold uppercase tracking-[0.18em] text-white/42">{{ __('Account') }}</p>
    <div class="space-y-1">
        @include('partials.sidebar-account-link')
    </div>
</div>
