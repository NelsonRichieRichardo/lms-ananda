@extends('layouts.dashboard')

@section('title', $course->title . ' - Gradebook')

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('teacher.gradebook.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to gradebook') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ $course->title }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('Gradebook') }}</p>
    </div>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-100 text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                        <th class="py-3 pe-4">{{ __('Assignment') }}</th>
                        <th class="py-3 pe-4">{{ __('Submissions') }}</th>
                        <th class="py-3 pe-4">{{ __('Graded') }}</th>
                        <th class="py-3 pe-4">{{ __('Average') }}</th>
                        <th class="py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($course->assignments as $assignment)
                        <tr>
                            <td class="py-3 pe-4 font-medium text-neutral-900">{{ $assignment->title }}</td>
                            <td class="py-3 pe-4 text-neutral-600">{{ $assignment->submissions->count() }}</td>
                            <td class="py-3 pe-4 text-neutral-600">{{ $assignment->submissions->whereNotNull('grade')->count() }}</td>
                            <td class="py-3 pe-4 text-neutral-600">
                                @if ($assignment->submissions->whereNotNull('grade')->count() > 0)
                                    {{ number_format($assignment->submissions->whereNotNull('grade')->avg('grade'), 2) }}
                                @else
                                    <span class="text-neutral-400">{{ __('—') }}</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <a href="{{ route('teacher.courses.assignments.submissions', [$course, $assignment]) }}" class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('View submissions') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-neutral-500">
                                {{ __('No assignments yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endsection
