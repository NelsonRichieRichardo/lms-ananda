@extends('layouts.dashboard')

@section('title', $assignment->title . ' - Submissions')

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to course') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ $assignment->title }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('Submissions') }}</p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-100 text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                        <th class="py-3 pe-4">{{ __('Student') }}</th>
                        <th class="py-3 pe-4">{{ __('Submitted') }}</th>
                        <th class="py-3 pe-4">{{ __('Content') }}</th>
                        <th class="py-3 pe-4">{{ __('Attachment') }}</th>
                        <th class="py-3 pe-4">{{ __('Grade') }}</th>
                        <th class="py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($submissions as $submission)
                        <tr>
                            <td class="py-3 pe-4 font-medium text-neutral-900">{{ $submission->user->name }}</td>
                            <td class="py-3 pe-4 text-neutral-600">
                                @if ($submission->submitted_at)
                                    {{ $submission->submitted_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                @else
                                    <span class="text-neutral-400">{{ __('Not submitted') }}</span>
                                @endif
                            </td>
                            <td class="py-3 pe-4 text-neutral-600 max-w-xs">
                                @if ($submission->content)
                                    {{ \Illuminate\Support\Str::limit(strip_tags($submission->content), 100) }}
                                @else
                                    <span class="text-neutral-400">{{ __('—') }}</span>
                                @endif
                            </td>
                            <td class="py-3 pe-4 text-neutral-600">
                                @if ($submission->attachment_path)
                                    <a href="{{ $submission->attachmentPublicUrl() }}" target="_blank" rel="noopener" class="text-[#6B1212] hover:text-[#5A1010]">
                                        {{ $submission->attachment_original_name ?: __('Download') }}
                                    </a>
                                @else
                                    <span class="text-neutral-400">{{ __('—') }}</span>
                                @endif
                            </td>
                            <td class="py-3 pe-4 text-neutral-600">
                                @if ($submission->grade)
                                    <span class="font-bold text-[#6B1212]">{{ number_format($submission->grade, 2) }}</span>
                                @else
                                    <span class="text-neutral-400">{{ __('Not graded') }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <button
                                    x-data="{ open: false }"
                                    @click="open = !open"
                                    class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]"
                                >
                                    {{ __('Grade') }}
                                </button>
                                
                                @if (isset($open) && $open)
                                    <div class="mt-4 rounded-xl border border-neutral-200 bg-white p-4">
                                        <form method="POST" action="{{ route('teacher.courses.assignments.submissions.grade', [$course, $assignment, $submission]) }}">
                                            @csrf
                                            <div class="mb-4">
                                                <x-input-label for="grade_{{ $submission->id }}" :value="__('Grade (0-100)')" />
                                                <input
                                                    id="grade_{{ $submission->id }}"
                                                    name="grade"
                                                    type="number"
                                                    min="0"
                                                    max="100"
                                                    step="0.01"
                                                    :value="old('grade', $submission->grade ?? '')"
                                                    class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                                                />
                                                <x-input-error class="mt-2" :messages="$errors->get('grade')" />
                                            </div>
                                            <div class="mb-4">
                                                <x-input-label for="comment_{{ $submission->id }}" :value="__('Feedback (optional)')" />
                                                <textarea
                                                    id="comment_{{ $submission->id }}"
                                                    name="grade_comment"
                                                    rows="3"
                                                    class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                                                >{{ old('grade_comment', $submission->grade_comment ?? '') }}</textarea>
                                                <x-input-error class="mt-2" :messages="$errors->get('grade_comment')" />
                                            </div>
                                            <div class="flex gap-2">
                                                <x-primary-button>{{ __('Save grade') }}</x-primary-button>
                                                <button type="button" @click="open = false" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-neutral-500">
                                {{ __('No submissions yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
