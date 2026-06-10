@extends('layouts.dashboard')

@section('title', __('Create course'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Create course') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('Add a title and optional details. A unique slug is generated automatically. You can add study materials anytime from the course page after saving.') }}</p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('teacher.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" class="mt-2" type="text" name="title" :value="old('title')" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="description" :value="__('Description')" />
                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    class="mt-2 block w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                >{{ old('description') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            <div>
                <x-input-label for="cover_photo" :value="__('Cover image (optional)')" />
                <input
                    id="cover_photo"
                    name="cover_photo"
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    class="mt-2 block w-full cursor-pointer rounded-xl border border-neutral-200 bg-neutral-50/50 px-3 py-2 text-sm text-neutral-800 file:me-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-neutral-900 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-neutral-800"
                />
                <p class="mt-1 text-xs text-neutral-500">{{ __('JPEG, PNG, GIF, or WebP. Max 5 MB.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('cover_photo')" />
            </div>

            <div class="flex items-center gap-2">
                <input
                    id="is_published"
                    name="is_published"
                    type="checkbox"
                    value="1"
                    class="rounded border-neutral-300 text-neutral-900 shadow-sm focus:ring-neutral-900/20"
                    {{ old('is_published', true) ? 'checked' : '' }}
                />
                <x-input-label for="is_published" :value="__('Published')" class="!mb-0" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save course') }}</x-primary-button>
                <a href="{{ route('teacher.courses.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </x-ui.card>
@endsection
