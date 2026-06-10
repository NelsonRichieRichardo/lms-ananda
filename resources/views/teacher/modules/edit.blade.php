@extends('layouts.dashboard')

@section('title', __('Edit study material'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to course') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Edit study material') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ $course->title }}</p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('teacher.courses.modules.update', [$course, $module]) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input id="title" class="mt-2" type="text" name="title" :value="old('title', $module->title)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="content" :value="__('Content')" />
                <textarea
                    id="content"
                    name="content"
                    rows="12"
                    class="mt-2 block w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                >{{ old('content', $module->content) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('content')" />
            </div>

            @if ($module->attachment_path)
                <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-3">
                    <p class="text-xs font-medium text-neutral-500">{{ __('Current file') }}</p>
                    <a href="{{ $module->attachmentPublicUrl() }}" target="_blank" rel="noopener" class="mt-1 inline-block text-sm font-medium text-neutral-900 underline underline-offset-2 hover:text-neutral-700">
                        {{ $module->attachment_original_name ?: __('Download') }}
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <input
                        id="remove_material_attachment"
                        name="remove_material_attachment"
                        type="checkbox"
                        value="1"
                        class="rounded border-neutral-300 text-neutral-900 shadow-sm focus:ring-neutral-900/20"
                        {{ old('remove_material_attachment') ? 'checked' : '' }}
                    />
                    <x-input-label for="remove_material_attachment" :value="__('Remove attached file')" class="!mb-0" />
                </div>
            @endif

            <div>
                <x-input-label for="mat_attachment" :value="__('Replace or add attachment (optional)')" />
                <input
                    id="mat_attachment"
                    name="mat_attachment"
                    type="file"
                    class="mt-2 block w-full cursor-pointer rounded-xl border border-neutral-200 bg-neutral-50/50 px-3 py-2 text-sm text-neutral-800 file:me-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-neutral-900 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-neutral-800"
                />
                <p class="mt-1 text-xs text-neutral-500">{{ __('PDF, Office docs, ZIP, images. Max 20 MB. Uploading replaces the current file.') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('mat_attachment')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>
                <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </x-ui.card>
@endsection
