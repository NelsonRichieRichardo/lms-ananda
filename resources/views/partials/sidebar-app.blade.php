@auth
    @if (auth()->user()->hasRole(\App\Support\RoleName::SuperAdmin))
        @include('partials.sidebar-admin')
    @elseif (auth()->user()->hasRole(\App\Support\RoleName::Teacher))
        @include('partials.sidebar-teacher')
    @else
        @include('partials.sidebar-student')
    @endif
@endauth
