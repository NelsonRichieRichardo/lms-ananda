@extends('layouts.dashboard')

@section('title', __('Student Dashboard'))

@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('page_heading', __('Student Dashboard'))

@section('content')
    @php
        $progressRows = collect($stats['progress_rows'] ?? []);
        $activeCourses = $stats['active_enrollments'] ?? 0;
        $completedCourses = $stats['completed_enrollments'] ?? 0;
        $avgModules = $stats['avg_modules_per_course'] ?? 0;
    @endphp

    <section class="mb-7 overflow-hidden rounded-[1.7rem] bg-[#5B1010] text-white shadow-xl shadow-[#6B1212]/10">
        <div class="relative grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_19rem] lg:p-9">
            <div class="absolute right-0 top-0 h-40 w-40 rounded-bl-full bg-white/8"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/55">{{ __('SMA Ananda Batam Learning Portal') }}</p>
                <h1 class="portal-heading mt-3 text-3xl font-bold leading-tight sm:text-4xl">
                    {{ __('Welcome, :name', ['name' => Auth::user()->name]) }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/70">
                    {{ __('Continue your courses, check today’s academic focus, and review learning activity from one official school portal.') }}
                </p>
            </div>
            <div class="relative z-10 rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.18em] text-white/50">{{ __('Student Record') }}</p>
                <p class="mt-2 text-sm font-bold">{{ Auth::user()->student_id ?? __('No student ID') }}</p>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-2xl font-bold">{{ $activeCourses }}</p>
                        <p class="text-xs text-white/60">{{ __('Active') }}</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold">{{ $completedCourses }}</p>
                        <p class="text-xs text-white/60">{{ __('Completed') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-7 grid gap-4 sm:grid-cols-3">
        <div class="portal-card rounded-3xl p-5">
            <p class="portal-label">{{ __('Enrolled Courses') }}</p>
            <p class="portal-heading mt-3 text-3xl font-bold text-[#6B1212]">{{ $stats['enrolled_courses'] ?? $activeCourses }}</p>
            <p class="mt-2 text-sm text-[#766A60]">{{ __('Total courses connected to your account.') }}</p>
        </div>
        <div class="portal-card rounded-3xl p-5">
            <p class="portal-label">{{ __('Active Learning') }}</p>
            <p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $activeCourses }}</p>
            <p class="mt-2 text-sm text-[#766A60]">{{ __('Courses currently in progress.') }}</p>
        </div>
        <div class="portal-card rounded-3xl p-5">
            <p class="portal-label">{{ __('Unread Notifications') }}</p>
            <p class="portal-heading mt-3 text-3xl font-bold text-[#201A17]">{{ $stats['unread_notifications_count'] ?? 0 }}</p>
            <p class="mt-2 text-sm text-[#766A60]">{{ __('New notifications waiting for you.') }}</p>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
        <div class="space-y-6">
            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <p class="portal-label">{{ __('Academic Focus') }}</p>
                        <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('My active courses') }}</h2>
                    </div>
                    <a href="{{ route('student.courses.index') }}" class="portal-button-secondary shrink-0">{{ __('Browse courses') }}</a>
                </div>

                <div class="space-y-3">
                    @forelse ($progressRows as $row)
                        @php
                            $moduleCount = (int) ($row['module_count'] ?? 0);
                            $assignmentCount = (int) ($row['assignment_count'] ?? 0);
                            $status = $row['status'] ?? 'active';
                            $progress = (int) ($row['progress'] ?? 0);
                            $courseId = $row['course_id'] ?? null;
                        @endphp
                        <a href="{{ $courseId ? route('student.courses.show', $courseId) : '#' }}" class="block rounded-3xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35 hover:shadow-md">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <p class="text-base font-bold text-[#201A17]">{{ $row['course_title'] ?: __('Untitled course') }}</p>
                                    <p class="mt-1 text-sm text-[#766A60]">
                                        {{ $moduleCount }} {{ __('materials') }} · {{ $assignmentCount }} {{ __('assignments') }}
                                        @if (! empty($row['enrolled_at']))
                                            · {{ __('Enrolled') }} {{ $row['enrolled_at']->format('M j, Y') }}
                                        @endif
                                    </p>
                                    @if ($progress > 0)
                                        <div class="mt-3 flex items-center gap-2">
                                            <div class="h-2 flex-1 rounded-full bg-[#E7DDD1]">
                                                <div class="h-2 rounded-full bg-[#6B1212]" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-[#6B1212]">{{ $progress }}%</span>
                                        </div>
                                    @endif
                                </div>
                                <span class="portal-badge capitalize shrink-0">{{ $status }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-dashed border-[#D7CABB] bg-[#FAF7F1] p-8 text-center">
                            <p class="font-bold text-[#201A17]">{{ __('No active course yet') }}</p>
                            <p class="mt-2 text-sm text-[#766A60]">{{ __('Open the course catalog and enroll when a published course is available.') }}</p>
                            <a href="{{ route('student.courses.index') }}" class="portal-button-primary mt-5">{{ __('Open course catalog') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Today') }}</p>
                <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Learning schedule') }}</h2>
                <div class="mt-5 space-y-3">
                    <div class="flex gap-4 rounded-3xl bg-[#FAF7F1] p-4">
                        <div class="w-16 shrink-0 text-center">
                            <p class="text-sm font-bold text-[#6B1212]">08:00</p>
                            <p class="text-xs text-[#766A60]">09:30</p>
                        </div>
                        <div>
                            <p class="font-bold text-[#201A17]">{{ __('Morning class session') }}</p>
                            <p class="mt-1 text-sm text-[#766A60]">{{ __('Check your assigned course material before class starts.') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4 rounded-3xl bg-white p-4 ring-1 ring-[#E7DDD1]">
                        <div class="w-16 shrink-0 text-center">
                            <p class="text-sm font-bold text-[#6B1212]">13:00</p>
                            <p class="text-xs text-[#766A60]">14:30</p>
                        </div>
                        <div>
                            <p class="font-bold text-[#201A17]">{{ __('Independent study') }}</p>
                            <p class="mt-1 text-sm text-[#766A60]">{{ __('Review materials and complete pending assignments.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Quick Links') }}</p>
                <h2 class="portal-heading mt-2 text-xl font-bold text-[#201A17]">{{ __('Access Tools') }}</h2>
                <div class="mt-5 space-y-3">
                    <a href="{{ route('calendar.index') }}" class="flex items-center gap-3 rounded-2xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#6B1212]/10 text-[#6B1212]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-[#201A17]">{{ __('Calendar') }}</p>
                            <p class="text-xs text-[#766A60]">{{ __('View events and deadlines') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('student.gradebook.index') }}" class="flex items-center gap-3 rounded-2xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#6B1212]/10 text-[#6B1212]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-[#201A17]">{{ __('Gradebook') }}</p>
                            <p class="text-xs text-[#766A60]">{{ __('View your grades') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Upcoming Assignments') }}</p>
                <h2 class="portal-heading mt-2 text-xl font-bold text-[#201A17]">{{ __('Due Soon') }}</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($stats['upcoming_assignments'] ?? collect() as $assignment)
                        <a href="{{ route('student.courses.assignments.show', [$assignment->course, $assignment]) }}" class="block rounded-2xl border-l-4 border-[#6B1212] bg-[#FAF7F1] p-4 transition hover:shadow-md">
                            <p class="text-sm font-bold text-[#201A17]">{{ $assignment->title }}</p>
                            <p class="mt-1 text-xs text-[#766A60]">{{ $assignment->course->title }}</p>
                            <p class="mt-2 text-xs font-bold text-[#6B1212]">{{ __('Due') }}: {{ $assignment->due_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-[#766A60]">{{ __('No upcoming assignments.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Recent Materials') }}</p>
                <h2 class="portal-heading mt-2 text-xl font-bold text-[#201A17]">{{ __('New Content') }}</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($stats['recent_materials'] ?? collect() as $module)
                        <a href="{{ route('student.courses.show', $module->course) }}" class="block rounded-2xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                            <p class="text-sm font-bold text-[#201A17]">{{ $module->title }}</p>
                            <p class="mt-1 text-xs text-[#766A60]">{{ $module->course->title }}</p>
                            <p class="mt-2 text-xs text-[#766A60]/60">{{ $module->created_at->diffForHumans() }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-[#766A60]">{{ __('No recent materials.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Notifications') }}</p>
                <h2 class="portal-heading mt-2 text-xl font-bold text-[#201A17]">{{ __('Recent Updates') }}</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($stats['recent_notifications'] ?? collect() as $notification)
                        <div class="rounded-2xl @if (!$notification->read_at) bg-[#6B1212]/5 @else bg-white @endif p-4">
                            <p class="text-sm font-bold text-[#201A17]">{{ $notification->data['title'] ?? __('Notification') }}</p>
                            <p class="mt-1 text-xs text-[#766A60]">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="mt-2 text-xs text-[#766A60]/60">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-[#766A60]">{{ __('No notifications yet.') }}</p>
                    @endforelse
                </div>
                @if (($stats['total_notifications_count'] ?? 0) > 3)
                    <a href="#" class="mt-3 block text-center text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('View all notifications') }}</a>
                @endif
            </div>

            <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Announcements') }}</p>
                <h2 class="portal-heading mt-2 text-xl font-bold text-[#201A17]">{{ __('School Notices') }}</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($stats['announcements'] ?? collect() as $announcement)
                        <div class="rounded-2xl @if ($announcement->is_pinned) border-l-4 border-[#6B1212] @else border border-[#E7DDD1] @endif bg-white p-4">
                            @if ($announcement->is_pinned)
                                <span class="inline-flex items-center gap-1 rounded-full bg-[#6B1212]/10 px-2 py-1 text-xs font-bold text-[#6B1212]">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17.586 3.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path></svg>
                                    {{ __('Pinned') }}
                                </span>
                            @endif
                            <p class="mt-2 text-sm font-bold text-[#201A17]">{{ $announcement->title }}</p>
                            <p class="mt-1 text-xs text-[#766A60]">{{ \Illuminate\Support\Str::limit($announcement->content, 120) }}</p>
                            <p class="mt-2 text-xs text-[#766A60]/60">
                                {{ $announcement->user->name }}
                                @if ($announcement->course)
                                    · {{ $announcement->course->title }}
                                @endif
                                · {{ $announcement->created_at->diffForHumans() }}
                            </p>
                            @if ($announcement->course)
                                <a href="{{ route('student.courses.show', $announcement->course) }}" class="mt-3 inline-block text-xs font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('View details') }}</a>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-[#766A60]">{{ __('No announcements yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>
@endsection
