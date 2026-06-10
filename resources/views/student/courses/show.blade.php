@extends('layouts.dashboard')

@section('title', $course->title)

@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('page_heading', $course->title)

@section('content')
    @php
        $moduleCount = $course->modules->count();
        $assignmentCount = $course->assignments->count();
    @endphp

    <section class="mb-6 overflow-hidden rounded-[1.7rem] bg-[#5B1010] text-white shadow-xl shadow-[#6B1212]/10">
        <div class="relative p-6 sm:p-8">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-white/8"></div>
            <a href="{{ route('student.courses.index') }}" class="relative z-10 inline-flex text-sm font-semibold text-white/75 hover:text-white">
                {{ __('← Back to courses') }}
            </a>
            <div class="relative z-10 mt-5 grid gap-6 lg:grid-cols-[1fr_18rem] lg:items-end">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/55">{{ __('Digital Classroom') }}</p>
                    <h1 class="portal-heading mt-3 text-3xl font-bold leading-tight sm:text-4xl">{{ $course->title }}</h1>
                    <p class="mt-3 text-sm text-white/72">{{ __('Teacher') }} · {{ $course->teacher?->name ?? __('Unassigned') }}</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/10 p-5">
                    <p class="text-xs uppercase tracking-[0.18em] text-white/50">{{ __('Course Status') }}</p>
                    @if ($enrollment)
                        <p class="mt-2 text-lg font-bold capitalize">{{ $enrollment->status->value }}</p>
                        <p class="mt-1 text-xs text-white/60">{{ __('Enrolled') }} {{ $enrollment->enrolled_at?->format('M j, Y') ?? __('—') }}</p>
                    @else
                        <p class="mt-2 text-lg font-bold">{{ __('Not enrolled') }}</p>
                        <p class="mt-1 text-xs text-white/60">{{ __('Enroll to access materials and assignments.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if (!$enrollment)
        <form method="POST" action="{{ route('student.courses.enroll', $course) }}" class="mb-6 rounded-[1.5rem] border border-[#E7DDD1] bg-[#FFFDF9] p-5 shadow-sm">
            @csrf
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-bold text-[#201A17]">{{ __('Join this classroom') }}</p>
                    <p class="mt-1 text-sm text-[#766A60]">{{ __('Enrollment is required before you can open study materials and assignments.') }}</p>
                </div>
                <button type="submit" class="portal-button-primary shrink-0">{{ __('Enroll now') }}</button>
            </div>
        </form>
    @endif

    <section class="grid gap-6 lg:grid-cols-[1fr_21rem]">
        <div class="space-y-6">
            @if ($course->coverPublicUrl())
                <img src="{{ $course->coverPublicUrl() }}" alt="" class="max-h-80 w-full rounded-[1.5rem] object-cover shadow-sm" />
            @endif

            <div id="overview" class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                <p class="portal-label">{{ __('Overview') }}</p>
                <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Course description') }}</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-[#766A60]">{{ $course->description ?: __('No description provided.') }}</p>
                
                @if ($enrollment)
                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-[#FAF7F1] p-4 text-center">
                            <p class="text-2xl font-bold text-[#6B1212]">{{ $moduleCount }}</p>
                            <p class="text-xs text-[#766A60]">{{ __('Learning Materials') }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FAF7F1] p-4 text-center">
                            <p class="text-2xl font-bold text-[#6B1212]">{{ $assignmentCount }}</p>
                            <p class="text-xs text-[#766A60]">{{ __('Assignments') }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#FAF7F1] p-4 text-center">
                            <p class="text-2xl font-bold text-[#6B1212]">{{ $enrollment->status->value }}</p>
                            <p class="text-xs text-[#766A60]">{{ __('Status') }}</p>
                        </div>
                    </div>

                    @if ($totalModulesCount > 0)
                        <div class="mt-6">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-sm font-bold text-[#201A17]">{{ __('Course Progress') }}</p>
                                <p class="text-sm font-bold text-[#6B1212]">{{ $completionPercentage }}%</p>
                            </div>
                            <div class="h-2 w-full rounded-full bg-[#E7DDD1]">
                                <div class="h-2 rounded-full bg-[#6B1212] transition-all duration-300" style="width: {{ $completionPercentage }}%"></div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            @if ($enrollment)
                <div id="materials" class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="portal-label">{{ __('Learning Modules') }}</p>
                            <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Study materials') }}</h2>
                        </div>
                        <span class="portal-badge">{{ $moduleCount }} {{ __('items') }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($course->modules as $module)
                            <article class="rounded-3xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                                <div class="flex gap-4">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl @if (isset($moduleProgress[$module->id]) && $moduleProgress[$module->id]->is_completed) bg-green-100 text-green-700 @else bg-[#6B1212]/10 text-[#6B1212] @endif text-sm font-bold">
                                        @if (isset($moduleProgress[$module->id]) && $moduleProgress[$module->id]->is_completed)
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            {{ $loop->iteration }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-[#201A17]">{{ $module->title }}</p>
                                        @if ($module->content)
                                            <p class="mt-1 text-sm leading-6 text-[#766A60]">{{ \Illuminate\Support\Str::limit(strip_tags($module->content), 160) }}</p>
                                        @endif
                                        @if ($module->attachmentPublicUrl())
                                            <a href="{{ $module->attachmentPublicUrl() }}" download class="mt-3 inline-flex text-sm font-bold text-[#6B1212] hover:text-[#4F0D0D]">
                                                {{ __('Download material') }}{{ $module->attachment_original_name ? ': '.$module->attachment_original_name : '' }}
                                            </a>
                                        @endif
                                        <div class="mt-3 flex gap-2">
                                            @if (isset($moduleProgress[$module->id]) && $moduleProgress[$module->id]->is_completed)
                                                <form method="POST" action="{{ route('student.courses.modules.incomplete', [$course, $module]) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-medium text-[#766A60] hover:text-[#201A17]">{{ __('Mark as incomplete') }}</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('student.courses.modules.complete', [$course, $module]) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs font-medium text-[#6B1212] hover:text-[#4F0D0D]">{{ __('Mark as complete') }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-[#D7CABB] bg-[#FAF7F1] p-8 text-center text-sm text-[#766A60]">
                                {{ __('No study materials in this course yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div id="assignments" class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div>
                            <p class="portal-label">{{ __('Class Work') }}</p>
                            <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Assignments') }}</h2>
                        </div>
                        <span class="portal-badge">{{ $assignmentCount }} {{ __('tasks') }}</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($course->assignments as $assignment)
                            <article class="rounded-3xl border border-[#E7DDD1] bg-white p-4 transition hover:border-[#6B1212]/35">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <a href="{{ route('student.courses.assignments.show', [$course, $assignment]) }}" class="font-bold text-[#201A17] hover:text-[#6B1212]">{{ $assignment->title }}</a>
                                        @if ($assignment->instructions)
                                            <p class="mt-1 text-sm leading-6 text-[#766A60]">{{ \Illuminate\Support\Str::limit($assignment->instructions, 120) }}</p>
                                        @endif
                                    </div>
                                    @if ($assignment->due_at)
                                        <div class="shrink-0 rounded-2xl bg-[#FAF7F1] px-4 py-3 text-left sm:text-right">
                                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ __('Due') }}</p>
                                            <p class="mt-1 text-sm font-bold text-[#6B1212]">{{ $assignment->due_at->timezone(config('app.timezone'))->format('M j, Y') }}</p>
                                            <p class="text-xs text-[#766A60]">{{ $assignment->due_at->timezone(config('app.timezone'))->format('g:i A') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-[#D7CABB] bg-[#FAF7F1] p-8 text-center text-sm text-[#766A60]">
                                {{ __('No assignments posted yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            <div class="portal-card rounded-[1.5rem] p-5">
                <p class="portal-label">{{ __('Teacher') }}</p>
                <div class="mt-4 flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#6B1212] text-lg font-bold text-white">
                        {{ $course->teacher?->name ? strtoupper(substr($course->teacher->name, 0, 1)) : '?' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-[#201A17]">{{ $course->teacher?->name ?? __('Unassigned') }}</p>
                        <p class="text-sm text-[#766A60]">{{ __('Course instructor') }}</p>
                        @if ($course->teacher?->email)
                            <p class="text-xs text-[#766A60]/60">{{ $course->teacher->email }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="portal-card rounded-[1.5rem] p-5">
                <p class="portal-label">{{ __('Course Information') }}</p>
                <div class="mt-4 space-y-3">
                    <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                        <span class="text-[#766A60]">{{ __('Created') }}</span>
                        <span class="font-bold text-[#201A17]">{{ $course->created_at->format('M j, Y') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                        <span class="text-[#766A60]">{{ __('Status') }}</span>
                        <span class="font-bold text-[#201A17]">{{ $course->is_published ? __('Published') : __('Draft') }}</span>
                    </div>
                    @if ($enrollment)
                        <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                            <span class="text-[#766A60]">{{ __('Enrolled') }}</span>
                            <span class="font-bold text-[#201A17]">{{ $enrollment->enrolled_at?->format('M j, Y') ?? __('—') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-[#766A60]">{{ __('Access') }}</span>
                        <span class="font-bold text-[#201A17]">{{ $enrollment ? __('Open') : __('Restricted') }}</span>
                    </div>
                </div>
            </div>

            <div class="portal-card rounded-[1.5rem] p-5">
                <p class="portal-label">{{ __('Classroom Summary') }}</p>
                <div class="mt-4 space-y-3">
                    <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                        <span class="text-[#766A60]">{{ __('Materials') }}</span>
                        <span class="font-bold text-[#201A17]">{{ $moduleCount }}</span>
                    </div>
                    <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                        <span class="text-[#766A60]">{{ __('Assignments') }}</span>
                        <span class="font-bold text-[#201A17]">{{ $assignmentCount }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#766A60]">{{ __('Access') }}</span>
                        <span class="font-bold text-[#201A17]">{{ $enrollment ? __('Open') : __('Restricted') }}</span>
                    </div>
                </div>
            </div>
        </aside>
    </section>
@endsection
