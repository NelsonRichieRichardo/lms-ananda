@extends('layouts.dashboard')

@section('title', __('Gradebook'))

@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('page_heading', __('Gradebook'))

@section('content')
    <section class="mb-7 overflow-hidden rounded-[1.7rem] bg-[#5B1010] text-white shadow-xl shadow-[#6B1212]/10">
        <div class="relative p-6 sm:p-8">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-white/8"></div>
            <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/55">{{ __('Academic Record') }}</p>
            <h1 class="portal-heading mt-3 text-3xl font-bold leading-tight sm:text-4xl">{{ __('My Grades') }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">{{ __('View your graded assignments and feedback from teachers across all your enrolled courses.') }}</p>
        </div>
    </section>

    @if ($overallAverage !== null)
        <section class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="portal-card rounded-[1.5rem] p-5 text-center">
                <p class="text-3xl font-bold text-[#6B1212]">{{ $overallAverage }}</p>
                <p class="text-xs text-[#766A60]">{{ __('Overall Average') }}</p>
            </div>
            <div class="portal-card rounded-[1.5rem] p-5 text-center">
                <p class="text-3xl font-bold text-[#6B1212]">{{ count($grades) }}</p>
                <p class="text-xs text-[#766A60]">{{ __('Graded Assignments') }}</p>
            </div>
            <div class="portal-card rounded-[1.5rem] p-5 text-center">
                <p class="text-3xl font-bold text-[#6B1212]">{{ count($courseMetrics) }}</p>
                <p class="text-xs text-[#766A60]">{{ __('Enrolled Courses') }}</p>
            </div>
        </section>
    @endif

    @if (count($courseMetrics) > 0)
        <section class="mb-6 portal-card rounded-[1.5rem] p-5 sm:p-6">
            <p class="portal-label">{{ __('Course Performance') }}</p>
            <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Performance by Course') }}</h2>
            <div class="mt-5 space-y-4">
                @foreach ($courseMetrics as $metric)
                    <div class="rounded-2xl border border-[#E7DDD1] bg-white p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-[#201A17]">{{ $metric['course']->title }}</p>
                                <p class="mt-1 text-xs text-[#766A60]">{{ $metric['graded_assignments'] }} / {{ $metric['total_assignments'] }} {{ __('assignments graded') }}</p>
                            </div>
                            <div class="text-right">
                                @if ($metric['average_grade'] !== null)
                                    <p class="text-2xl font-bold text-[#6B1212]">{{ $metric['average_grade'] }}</p>
                                    <p class="text-xs text-[#766A60]">{{ __('Average') }}</p>
                                @else
                                    <p class="text-sm text-[#766A60]">{{ __('No grades yet') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 h-2 w-full rounded-full bg-[#E7DDD1]">
                            <div class="h-2 rounded-full bg-[#6B1212] transition-all duration-300" style="width: {{ $metric['completion_rate'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="portal-card rounded-[1.5rem] p-5 sm:p-6">
        <p class="portal-label">{{ __('Assignment Grades') }}</p>
        <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Detailed Grades') }}</h2>
        <div class="mt-5">
            @forelse ($grades as $grade)
                <div class="mb-6 rounded-3xl border border-[#E7DDD1] bg-white p-5 last:mb-0">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ $grade['course']->title }}</p>
                            <h2 class="mt-2 text-xl font-bold text-[#201A17]">{{ $grade['assignment']->title }}</h2>
                            <p class="mt-1 text-sm text-[#766A60]">{{ __('Graded') }} {{ $grade['submission']->graded_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                            
                            @if ($grade['submission']->grade_comment)
                                <div class="mt-4 rounded-2xl bg-[#FAF7F1] p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ __('Teacher Feedback') }}</p>
                                    <p class="mt-2 text-sm leading-6 text-[#201A17]">{{ $grade['submission']->grade_comment }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="shrink-0 rounded-3xl bg-[#6B1212]/10 p-5 text-center">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#6B1212]">{{ __('Grade') }}</p>
                            <p class="mt-2 text-3xl font-bold text-[#6B1212]">{{ number_format($grade['submission']->grade, 2) }}</p>
                            <p class="mt-1 text-xs text-[#766A60]">{{ __('out of 100') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-[#D7CABB] bg-[#FAF7F1] p-12 text-center">
                    <p class="portal-heading text-2xl font-bold text-[#201A17]">{{ __('No grades yet') }}</p>
                    <p class="mt-2 text-sm text-[#766A60]">{{ __('Your graded assignments will appear here once teachers have reviewed your submissions.') }}</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
