@extends('layouts.dashboard')

@section('title', $assignment->title)

@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('page_heading', $assignment->title)

@section('content')
    @php
        $isEnrolled = $enrollment ?? false;
    @endphp

    <section class="mb-6 overflow-hidden rounded-[1.7rem] bg-[#5B1010] text-white shadow-xl shadow-[#6B1212]/10">
        <div class="relative p-6 sm:p-8">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-white/8"></div>
            <a href="{{ route('student.courses.show', $course) }}" class="relative z-10 inline-flex text-sm font-semibold text-white/75 hover:text-white">
                {{ __('← Back to course') }}
            </a>
            <div class="relative z-10 mt-5">
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/55">{{ __('Assignment') }}</p>
                <h1 class="portal-heading mt-3 text-3xl font-bold leading-tight sm:text-4xl">{{ $assignment->title }}</h1>
                <p class="mt-3 text-sm text-white/72">{{ __('Course') }} · {{ $course->title }}</p>
            </div>
        </div>
    </section>

    @if (!$isEnrolled)
        <div class="mb-6 rounded-[1.5rem] border border-[#E7DDD1] bg-[#FFFDF9] p-5 shadow-sm">
            <p class="font-bold text-[#201A17]">{{ __('Enrollment required') }}</p>
            <p class="mt-1 text-sm text-[#766A60]">{{ __('You must be enrolled in this course to view and submit assignments.') }}</p>
        </div>
    @else
        <section class="grid gap-6 lg:grid-cols-[1fr_21rem]">
            <div class="space-y-6">
                <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                    <p class="portal-label">{{ __('Instructions') }}</p>
                    <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Assignment details') }}</h2>
                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-[#766A60]">{{ $assignment->instructions ?: __('No instructions provided.') }}</div>
                    
                    @if ($assignment->due_at)
                        <div class="mt-5 rounded-2xl bg-[#FAF7F1] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ __('Due Date') }}</p>
                            <p class="mt-1 text-sm font-bold text-[#6B1212]">{{ $assignment->due_at->timezone(config('app.timezone'))->format('M j, Y') }}</p>
                            <p class="text-xs text-[#766A60]">{{ $assignment->due_at->timezone(config('app.timezone'))->format('g:i A') }}</p>
                        </div>
                    @endif
                </div>

                <div class="portal-card rounded-[1.5rem] p-5 sm:p-6">
                    <p class="portal-label">{{ __('Your Work') }}</p>
                    <h2 class="portal-heading mt-2 text-2xl font-bold text-[#201A17]">{{ __('Submit assignment') }}</h2>
                    
                    @if ($submission)
                        <div class="mt-5 rounded-2xl border border-[#E7DDD1] bg-[#FAF7F1] p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ __('Submitted') }}</p>
                            <p class="mt-1 text-sm font-bold text-[#201A17]">{{ $submission->submitted_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>

                            @if ($submission->grade)
                                <div class="mt-4 rounded-2xl bg-[#6B1212]/10 p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#6B1212]">{{ __('Grade') }}</p>
                                    <p class="mt-1 text-2xl font-bold text-[#6B1212]">{{ number_format($submission->grade, 2) }}</p>
                                    @if ($submission->grade_comment)
                                        <p class="mt-2 text-sm text-[#766A60]">{{ $submission->grade_comment }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if ($submission->history->count() > 0)
                            <div class="mt-5 rounded-2xl border border-[#E7DDD1] bg-white p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ __('Submission History') }}</p>
                                <div class="mt-3 space-y-3">
                                    @foreach ($submission->history as $history)
                                        <div class="rounded-xl bg-[#FAF7F1] p-3">
                                            <p class="text-xs font-medium text-[#766A60]">{{ $history->submitted_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                                            @if ($history->content)
                                                <p class="mt-1 text-sm text-[#201A17]">{{ \Illuminate\Support\Str::limit($history->content, 150) }}</p>
                                            @endif
                                            @if ($history->attachment_path)
                                                <p class="mt-1 text-xs text-[#766A60]">{{ __('File') }}: {{ $history->attachment_original_name }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('student.courses.assignments.submit', [$course, $assignment]) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf
                        
                        @if (session('status') == 'submission-saved')
                            <div class="rounded-xl bg-green-50 p-4 text-sm text-green-800">
                                {{ __('Your submission has been saved successfully.') }}
                            </div>
                        @endif

                        <div>
                            <x-input-label for="content" :value="__('Your answer (optional)')" />
                            <textarea
                                id="content"
                                name="content"
                                rows="8"
                                class="mt-2 block w-full rounded border border-[#D4D0C8] bg-white px-3 py-2 text-sm text-[#1A1A1A] focus:border-[#6B1212] focus:outline-none focus:ring-1 focus:ring-[#6B1212]/20"
                            >{{ old('content', $submission->content ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>

                        <div>
                            <x-input-label for="attachment" :value="__('Attach file (optional)')" />
                            <input
                                id="attachment"
                                name="attachment"
                                type="file"
                                class="mt-2 block w-full cursor-pointer rounded border border-[#D4D0C8] bg-white px-3 py-2 text-sm text-[#1A1A1A] file:me-3 file:cursor-pointer file:rounded file:border-0 file:bg-[#6B1212] file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-[#5A1010]"
                            />
                            <p class="mt-1 text-xs text-[#1A1A1A]/60">{{ __('PDF, Office docs, ZIP, images. Max 20 MB.') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('attachment')" />
                            
                            @if ($submission && $submission->attachment_path)
                                <div class="mt-3 rounded-xl border border-neutral-100 bg-neutral-50 p-3">
                                    <p class="text-xs font-medium text-neutral-500">{{ __('Current file') }}</p>
                                    <a href="{{ $submission->attachmentPublicUrl() }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-medium text-neutral-900 underline underline-offset-2 hover:text-neutral-700">
                                        {{ $submission->attachment_original_name ?: __('Download') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <x-primary-button>{{ $submission ? __('Update submission') : __('Submit assignment') }}</x-primary-button>
                    </form>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="portal-card rounded-[1.5rem] p-5">
                    <p class="portal-label">{{ __('Assignment Info') }}</p>
                    <div class="mt-4 space-y-3">
                        <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                            <span class="text-[#766A60]">{{ __('Course') }}</span>
                            <span class="font-bold text-[#201A17]">{{ $course->title }}</span>
                        </div>
                        @if ($assignment->due_at)
                            <div class="flex justify-between border-b border-[#E7DDD1] pb-3 text-sm">
                                <span class="text-[#766A60]">{{ __('Due') }}</span>
                                <span class="font-bold text-[#201A17]">{{ $assignment->due_at->timezone(config('app.timezone'))->format('M j, Y') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-[#766A60]">{{ __('Status') }}</span>
                            <span class="font-bold text-[#201A17]">{{ $submission ? ($submission->grade ? 'Graded' : 'Submitted') : 'Not submitted' }}</span>
                        </div>
                    </div>
                </div>

                <div class="portal-card rounded-[1.5rem] p-5">
                    <p class="portal-label">{{ __('Teacher') }}</p>
                    <div class="mt-4 flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#6B1212] text-lg font-bold text-white">
                            {{ $course->teacher?->name ? strtoupper(substr($course->teacher->name, 0, 1)) : '?' }}
                        </div>
                        <div>
                            <p class="font-bold text-[#201A17]">{{ $course->teacher?->name ?? __('Unassigned') }}</p>
                            <p class="text-sm text-[#766A60]">{{ __('Course instructor') }}</p>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    @endif
@endsection
