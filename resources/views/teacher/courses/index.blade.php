@extends('layouts.dashboard')

@section('title', __('My Courses'))

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-[#D4D0C8] pb-6">
        <div>
            <h1 class="text-3xl font-semibold font-merriweather text-[#1A1A1A]">{{ __('My Courses') }}</h1>
            <p class="mt-2 text-sm text-[#1A1A1A]/70">{{ __('Create, publish, and manage your course catalog') }}</p>
        </div>
        <a
            href="{{ route('teacher.courses.create') }}"
            class="inline-flex items-center justify-center rounded border border-[#6B1212] bg-[#6B1212] px-6 py-2.5 text-sm font-medium text-white transition hover:bg-[#5A1010]"
        >
            {{ __('New Course') }}
        </a>
    </div>

    <div class="rounded border border-[#D4D0C8] bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[#D4D0C8] text-sm">
                <thead>
                    <tr class="bg-[#FAF8F5]">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#1A1A1A] uppercase tracking-wide">{{ __('Course') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#1A1A1A] uppercase tracking-wide">{{ __('Slug') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#1A1A1A] uppercase tracking-wide">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#1A1A1A] uppercase tracking-wide">{{ __('Students') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#1A1A1A] uppercase tracking-wide">{{ __('Updated') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-[#1A1A1A] uppercase tracking-wide">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#D4D0C8]">
                    @forelse ($courses as $course)
                        <tr class="hover:bg-[#FAF8F5]">
                            <td class="px-6 py-4">
                                <p class="font-medium text-[#1A1A1A]">{{ $course->title }}</p>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-[#1A1A1A]/70">{{ $course->slug }}</td>
                            <td class="px-6 py-4">
                                @if ($course->is_published)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ __('Published') }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">{{ __('Draft') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[#1A1A1A]/70">{{ $course->enrollments_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-[#1A1A1A]/70">{{ $course->updated_at->format('M j, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('teacher.courses.show', $course) }}" class="font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('View') }}</a>
                                <span class="mx-2 text-[#D4D0C8]">|</span>
                                <a href="{{ route('teacher.courses.edit', $course) }}" class="font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('Edit') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-lg font-medium text-[#1A1A1A]">{{ __('No courses yet') }}</p>
                                <p class="mt-2 text-sm text-[#1A1A1A]/70">{{ __('Create your first course to get started.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[#D4D0C8] px-6 py-4">
            {{ $courses->links() }}
        </div>
    </div>
@endsection
