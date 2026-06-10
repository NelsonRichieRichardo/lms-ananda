@extends('layouts.dashboard')

@section('title', __('Gradebook'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Gradebook') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('View grades across all your courses') }}</p>
    </div>

    <x-ui.card>
        <div class="divide-y divide-neutral-100">
            @forelse ($courses as $course)
                <div class="py-4 first:pt-0 last:pb-0">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-neutral-900">{{ $course->title }}</p>
                            <p class="mt-1 text-sm text-neutral-600">{{ $course->assignments->count() }} {{ __('assignments') }}</p>
                        </div>
                        <a href="{{ route('teacher.gradebook.show', $course) }}" class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('View grades') }}</a>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-neutral-500">
                    {{ __('No courses yet.') }}
                </div>
            @endforelse
        </div>
    </x-ui.card>
@endsection
