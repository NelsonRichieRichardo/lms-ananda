@extends('layouts.dashboard')

@section('title', __('Teacher Dashboard'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('page_heading', __('Teacher Dashboard'))

@section('content')
    <section class="mb-7 rounded-[1.7rem] bg-[#5B1010] p-6 text-white shadow-xl shadow-[#6B1212]/10 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/55">{{ __('Teacher Workspace') }}</p>
                <h1 class="portal-heading mt-3 text-3xl font-bold leading-tight sm:text-4xl">{{ __('Good day, :name', ['name' => Auth::user()->name]) }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">{{ __('Manage your courses, prepare learning materials, and monitor classroom activity from the school LMS.') }}</p>
            </div>
            <a href="{{ route('teacher.courses.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-[#5B1010] transition hover:bg-[#FAF7F1]">
                {{ __('Create course') }}
            </a>
        </div>
    </section>

    <section class="mb-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Total Courses') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#6B1212]">{{ $stats['courses_count'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Published') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['published_courses'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Active Students') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['active_enrollments'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Pending Grading') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['pending_submissions'] ?? 0 }}</p></div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="portal-label">{{ __('Teaching Focus') }}</p>
                    <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Course management') }}</h2>
                </div>
                <a href="{{ route('teacher.courses.index') }}" class="portal-button-secondary">{{ __('View courses') }}</a>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <a href="{{ route('teacher.courses.index') }}" class="rounded-3xl border border-[#E7DDD1] bg-white p-5 transition hover:border-[#6B1212]/35 hover:shadow-md">
                    <p class="font-bold text-[#201A17]">{{ __('Prepare materials') }}</p>
                    <p class="mt-2 text-sm leading-6 text-[#766A60]">{{ __('Open a course to add modules, attachments, and classroom resources.') }}</p>
                </a>
                <a href="{{ route('teacher.courses.index') }}" class="rounded-3xl border border-[#E7DDD1] bg-white p-5 transition hover:border-[#6B1212]/35 hover:shadow-md">
                    <p class="font-bold text-[#201A17]">{{ __('Post assignments') }}</p>
                    <p class="mt-2 text-sm leading-6 text-[#766A60]">{{ __('Create assignment instructions and due dates inside each course.') }}</p>
                </a>
            </div>
        </div>

        <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
            <p class="portal-label">{{ __('Quick Links') }}</p>
            <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Access Tools') }}</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('calendar.index') }}" class="flex items-center gap-3 rounded-2xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#6B1212]/10 text-[#6B1212]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-[#201A17]">{{ __('Calendar') }}</p>
                        <p class="text-xs text-[#766A60]">{{ __('View events and deadlines') }}</p>
                    </div>
                </a>
                <a href="{{ route('teacher.gradebook.index') }}" class="flex items-center gap-3 rounded-2xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#6B1212]/10 text-[#6B1212]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <p class="font-bold text-[#201A17]">{{ __('Gradebook') }}</p>
                        <p class="text-xs text-[#766A60]">{{ __('View student grades') }}</p>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection
