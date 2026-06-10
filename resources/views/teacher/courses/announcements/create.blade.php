@extends('layouts.dashboard')

@section('title', __('New Announcement'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('teacher.courses.announcements.index', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to announcements') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ __('New announcement') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ $course->title }}</p>
    </div>

    <x-ui.card>
        <form method="POST" action="{{ route('teacher.courses.announcements.store', $course) }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <input
                    id="title"
                    name="title"
                    type="text"
                    required
                    class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                    value="{{ old('title') }}"
                />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="content" :value="__('Content')" />
                <textarea
                    id="content"
                    name="content"
                    rows="6"
                    required
                    class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                >{{ old('content') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('content')" />
            </div>

            <div>
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_pinned"
                        value="1"
                        class="h-4 w-4 rounded border-neutral-300 text-[#6B1212] focus:ring-[#6B1212]"
                        {{ old('is_pinned') ? 'checked' : '' }}
                    />
                    <span class="ml-2 text-sm text-neutral-700">{{ __('Pin this announcement') }}</span>
                </label>
                <p class="mt-1 text-xs text-neutral-500">{{ __('Pinned announcements appear at the top of the list') }}</p>
            </div>

            <div class="flex gap-3">
                <x-primary-button>{{ __('Create announcement') }}</x-primary-button>
                <a href="{{ route('teacher.courses.announcements.index', $course) }}" class="px-4 py-2 text-sm font-medium text-neutral-700 hover:text-neutral-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </x-ui.card>
@endsection
