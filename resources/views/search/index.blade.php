@extends('layouts.dashboard')

@section('title', __('Search'))

@section('sidebar')
    @if (auth()->user()->hasRole('student'))
        @include('partials.sidebar-student')
    @elseif (auth()->user()->hasRole('teacher'))
        @include('partials.sidebar-teacher')
    @elseif (auth()->user()->hasRole('super-admin'))
        @include('partials.sidebar-admin')
    @endif
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Search') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('Search across courses, assignments, learning materials, and students') }}</p>
    </div>

    <x-ui.card>
        <form method="GET" action="{{ route('search.index') }}" class="mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input
                        type="text"
                        name="q"
                        value="{{ old('q', $query) }}"
                        placeholder="{{ __('Search...') }}"
                        class="w-full rounded border border-neutral-200 px-4 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                    />
                </div>
                <div>
                    <select
                        name="type"
                        class="rounded border border-neutral-200 px-4 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                    >
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option value="courses" {{ $type === 'courses' ? 'selected' : '' }}>{{ __('Courses') }}</option>
                        <option value="assignments" {{ $type === 'assignments' ? 'selected' : '' }}>{{ __('Assignments') }}</option>
                        <option value="modules" {{ $type === 'modules' ? 'selected' : '' }}>{{ __('Learning Materials') }}</option>
                        @if (auth()->user()->hasRole('super-admin'))
                            <option value="students" {{ $type === 'students' ? 'selected' : '' }}>{{ __('Students') }}</option>
                        @endif
                    </select>
                </div>
                <x-primary-button>{{ __('Search') }}</x-primary-button>
            </div>
        </form>

        @if ($query)
            <div class="space-y-6">
                @if ($courses->count() > 0)
                    <div>
                        <h2 class="mb-4 text-lg font-semibold text-neutral-900">{{ __('Courses') }} ({{ $courses->count() }})</h2>
                        <div class="divide-y divide-neutral-100">
                            @foreach ($courses as $course)
                                <div class="py-4">
                                    <a href="{{ route('student.courses.show', $course) }}" class="font-medium text-neutral-900 hover:text-[#6B1212]">{{ $course->title }}</a>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $course->teacher->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($assignments->count() > 0)
                    <div>
                        <h2 class="mb-4 text-lg font-semibold text-neutral-900">{{ __('Assignments') }} ({{ $assignments->count() }})</h2>
                        <div class="divide-y divide-neutral-100">
                            @foreach ($assignments as $assignment)
                                <div class="py-4">
                                    <p class="font-medium text-neutral-900">{{ $assignment->title }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $assignment->course->title }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($modules->count() > 0)
                    <div>
                        <h2 class="mb-4 text-lg font-semibold text-neutral-900">{{ __('Learning Materials') }} ({{ $modules->count() }})</h2>
                        <div class="divide-y divide-neutral-100">
                            @foreach ($modules as $module)
                                <div class="py-4">
                                    <p class="font-medium text-neutral-900">{{ $module->title }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $module->course->title }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($students->count() > 0 && auth()->user()->hasRole('super-admin'))
                    <div>
                        <h2 class="mb-4 text-lg font-semibold text-neutral-900">{{ __('Students') }} ({{ $students->count() }})</h2>
                        <div class="divide-y divide-neutral-100">
                            @foreach ($students as $student)
                                <div class="py-4">
                                    <p class="font-medium text-neutral-900">{{ $student->name }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $student->email }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($courses->count() === 0 && $assignments->count() === 0 && $modules->count() === 0 && $students->count() === 0)
                    <div class="py-8 text-center text-sm text-neutral-500">
                        {{ __('No results found for ":query"', ['query' => $query]) }}
                    </div>
                @endif
            </div>
        @else
            <div class="py-8 text-center text-sm text-neutral-500">
                {{ __('Enter a search term to find courses, assignments, and students.') }}
            </div>
        @endif
    </x-ui.card>
@endsection
