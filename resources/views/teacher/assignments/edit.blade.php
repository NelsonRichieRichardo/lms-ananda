@extends('layouts.dashboard')

@section('title', __('Edit assignment'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to course') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Edit assignment') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ $course->title }}</p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('teacher.courses.assignments.update', [$course, $assignment]) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" class="mt-2" type="text" name="title" :value="old('title', $assignment->title)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="instructions" :value="__('Instructions')" />
                <textarea
                    id="instructions"
                    name="instructions"
                    rows="10"
                    class="mt-2 block w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                >{{ old('instructions', $assignment->instructions) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('instructions')" />
            </div>

            <div>
                <x-input-label for="due_at" :value="__('Due date & time (optional)')" />
                <x-text-input
                    id="due_at"
                    class="mt-2"
                    type="datetime-local"
                    name="due_at"
                    :value="old('due_at', $assignment->due_at?->timezone(config('app.timezone'))->format('Y-m-d\TH:i'))"
                />
                <x-input-error class="mt-2" :messages="$errors->get('due_at')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </x-ui.card>
@endsection
