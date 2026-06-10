@extends('layouts.dashboard')

@section('title', $course->title)

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#1A1A1A]">{{ $course->title }}</h1>
            <p class="mt-1 text-sm text-[#1A1A1A]/70">{{ __('Slug') }}: <span class="font-mono text-[#1A1A1A]">{{ $course->slug }}</span></p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a
                href="{{ route('teacher.courses.edit', $course) }}"
                class="inline-flex items-center justify-center rounded border border-[#6B1212] bg-[#6B1212] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#5A1010]"
            >
                {{ __('Edit course') }}
            </a>
            <form method="POST" action="{{ route('teacher.courses.destroy', $course) }}" onsubmit="return confirm('{{ __('Delete this course?') }}');">
                @csrf
                @method('DELETE')
                <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-ui.card title="{{ __('Details') }}">
                @if ($course->coverPublicUrl())
                    <img src="{{ $course->coverPublicUrl() }}" alt="" class="mb-4 max-h-48 w-full rounded object-cover" />
                @endif
                <p class="whitespace-pre-line text-sm text-[#1A1A1A]">{{ $course->description ?: __('No description provided.') }}</p>
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded border border-[#D4D0C8] bg-[#FAF8F5] px-4 py-3">
                        <dt class="text-xs text-[#1A1A1A]/60">{{ __('Published') }}</dt>
                        <dd class="mt-1 font-medium text-[#1A1A1A]">{{ $course->is_published ? __('Yes') : __('No') }}</dd>
                    </div>
                    <div class="rounded border border-[#D4D0C8] bg-[#FAF8F5] px-4 py-3">
                        <dt class="text-xs text-[#1A1A1A]/60">{{ __('Last updated') }}</dt>
                        <dd class="mt-1 font-medium text-[#1A1A1A]">{{ $course->updated_at->format('M j, Y') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            <div class="mt-6">
                <x-ui.card title="{{ __('Study materials') }}">
                    <div class="divide-y divide-[#D4D0C8]">
                        @forelse ($course->modules as $module)
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-[#1A1A1A]">{{ $module->title }}</p>
                                        @if ($module->attachment_path)
                                            <p class="mt-1 text-xs text-[#1A1A1A]/60">{{ __('File attached') }}{{ $module->attachment_original_name ? ': '.$module->attachment_original_name : '' }}</p>
                                        @endif
                                        @if ($module->content)
                                            <p class="mt-2 text-sm text-[#1A1A1A]/70">{{ \Illuminate\Support\Str::limit(strip_tags($module->content), 120) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 gap-3">
                                        <a href="{{ route('teacher.courses.modules.edit', [$course, $module]) }}" class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('teacher.courses.modules.destroy', [$course, $module]) }}" class="inline" onsubmit="return confirm('{{ __('Remove this material?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">{{ __('Remove') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-[#1A1A1A]/60">
                                {{ __('No study materials yet. Add one below.') }}
                            </div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('teacher.courses.modules.store', $course) }}" enctype="multipart/form-data" class="mt-6 space-y-4 border-t border-[#D4D0C8] pt-6">
                        @csrf
                        <p class="text-sm font-medium text-[#1A1A1A]">{{ __('Add another study material') }}</p>
                        <div>
                            <x-input-label for="mat_title" :value="__('Title')" />
                            <x-text-input id="mat_title" class="mt-2" type="text" name="mat_title" :value="old('mat_title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>
                        <div>
                            <x-input-label for="mat_content" :value="__('Content (optional if you attach a file)')" />
                            <textarea
                                id="mat_content"
                                name="mat_content"
                                rows="5"
                                class="mt-2 block w-full rounded border border-[#D4D0C8] bg-white px-3 py-2 text-sm text-[#1A1A1A] focus:border-[#6B1212] focus:outline-none focus:ring-1 focus:ring-[#6B1212]/20"
                            >{{ old('mat_content') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('content')" />
                        </div>
                        <div>
                            <x-input-label for="mat_attachment" :value="__('Attachment (optional)')" />
                            <input
                                id="mat_attachment"
                                name="mat_attachment"
                                type="file"
                                class="mt-2 block w-full cursor-pointer rounded border border-[#D4D0C8] bg-white px-3 py-2 text-sm text-[#1A1A1A] file:me-3 file:cursor-pointer file:rounded file:border-0 file:bg-[#6B1212] file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-[#5A1010]"
                            />
                            <p class="mt-1 text-xs text-[#1A1A1A]/60">{{ __('PDF, Office docs, ZIP, images. Max 20 MB.') }}</p>
                            <x-input-error class="mt-2" :messages="$errors->get('mat_attachment')" />
                        </div>
                        <x-primary-button>{{ __('Add material') }}</x-primary-button>
                    </form>
                </x-ui.card>
            </div>

            <div class="mt-6">
                <x-ui.card title="{{ __('Assignments') }}">
                    <div class="divide-y divide-[#D4D0C8]">
                        @forelse ($course->assignments as $assignment)
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-[#1A1A1A]">{{ $assignment->title }}</p>
                                        @if ($assignment->due_at)
                                            <p class="mt-1 text-sm text-[#1A1A1A]/70">{{ __('Due') }}: {{ $assignment->due_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 gap-3">
                                        <a href="{{ route('teacher.courses.assignments.submissions', [$course, $assignment]) }}" class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('Submissions') }}</a>
                                        <a href="{{ route('teacher.courses.assignments.edit', [$course, $assignment]) }}" class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('teacher.courses.assignments.destroy', [$course, $assignment]) }}" class="inline" onsubmit="return confirm('{{ __('Remove this assignment?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">{{ __('Remove') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-sm text-[#1A1A1A]/60">
                                {{ __('No assignments yet. Add one below.') }}
                            </div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('teacher.courses.assignments.store', $course) }}" class="mt-6 space-y-4 border-t border-[#D4D0C8] pt-6">
                        @csrf
                        <p class="text-sm font-medium text-[#1A1A1A]">{{ __('Add assignment') }}</p>
                        <div>
                            <x-input-label for="as_title" :value="__('Title')" />
                            <x-text-input id="as_title" class="mt-2" type="text" name="as_title" :value="old('as_title')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>
                        <div>
                            <x-input-label for="as_instructions" :value="__('Instructions')" />
                            <textarea
                                id="as_instructions"
                                name="as_instructions"
                                rows="4"
                                class="mt-2 block w-full rounded border border-[#D4D0C8] bg-white px-3 py-2 text-sm text-[#1A1A1A] focus:border-[#6B1212] focus:outline-none focus:ring-1 focus:ring-[#6B1212]/20"
                            >{{ old('as_instructions') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('instructions')" />
                        </div>
                        <div>
                            <x-input-label for="as_due_at" :value="__('Due date & time (optional)')" />
                            <x-text-input id="as_due_at" class="mt-2" type="datetime-local" name="as_due_at" :value="old('as_due_at')" />
                            <x-input-error class="mt-2" :messages="$errors->get('due_at')" />
                        </div>
                        <x-primary-button>{{ __('Add assignment') }}</x-primary-button>
                    </form>
                </x-ui.card>
            </div>
        </div>

        <div>
            <x-ui.card title="{{ __('Summary') }}">
                <p class="text-sm text-[#1A1A1A]/70">
                    {{ trans_choice('{0} No study materials|{1} :count study material|[2,*] :count study materials', $course->modules->count(), ['count' => $course->modules->count()]) }}
                    ·
                    {{ trans_choice('{0} no assignments|{1} :count assignment|[2,*] :count assignments', $course->assignments->count(), ['count' => $course->assignments->count()]) }}
                </p>
                <p class="mt-3 text-xs text-[#1A1A1A]/60">{{ __('Students see published courses in Discover courses, then enroll to open materials and assignments.') }}</p>
            </x-ui.card>
        </div>
    </div>
@endsection
