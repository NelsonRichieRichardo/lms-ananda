@extends('layouts.dashboard')

@section('title', __('Admin Dashboard'))

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('page_heading', __('Admin Dashboard'))

@section('content')
    <section class="mb-7 rounded-[1.7rem] bg-[#5B1010] p-6 text-white shadow-xl shadow-[#6B1212]/10 sm:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/55">{{ __('School Administration') }}</p>
        <h1 class="portal-heading mt-3 text-3xl font-bold leading-tight sm:text-4xl">{{ __('SMA Ananda Batam overview') }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">{{ __('Monitor accounts, courses, and enrollments from the official LMS administration area.') }}</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Users') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#6B1212]">{{ $stats['users_count'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Teachers') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['teachers_count'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Students') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['students_count'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Courses') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['courses_count'] }}</p></div>
        <div class="portal-card rounded-3xl p-5"><p class="portal-label">{{ __('Enrollments') }}</p><p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['enrollments_count'] }}</p></div>
    </section>

    <section class="mt-7 grid gap-6 lg:grid-cols-2">
        <div class="portal-card rounded-[1.5rem] p-6">
            <p class="portal-label">{{ __('Student Management') }}</p>
            <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Manage student accounts') }}</h2>
            <p class="mt-3 text-sm leading-6 text-[#766A60]">{{ __('Create student accounts manually or import them in bulk using the provided CSV workflow.') }}</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('admin.students.index') }}" class="portal-button-primary">{{ __('Open students') }}</a>
                <a href="{{ route('admin.students.import') }}" class="portal-button-secondary">{{ __('Import CSV') }}</a>
            </div>
        </div>
        <div class="portal-card rounded-[1.5rem] p-6">
            <p class="portal-label">{{ __('Teacher Management') }}</p>
            <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Manage teacher accounts') }}</h2>
            <p class="mt-3 text-sm leading-6 text-[#766A60]">{{ __('Create teacher accounts and manage access for instructors.') }}</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('admin.teachers.index') }}" class="portal-button-primary">{{ __('Open teachers') }}</a>
                <a href="{{ route('admin.teachers.create') }}" class="portal-button-secondary">{{ __('Add teacher') }}</a>
            </div>
        </div>
    </section>
@endsection
