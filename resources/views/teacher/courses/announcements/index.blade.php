@extends('layouts.dashboard')

@section('title', $course->title . ' - Announcements')

@section('sidebar')
    @include('partials.sidebar-teacher')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to course') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ $course->title }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('Announcements') }}</p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm text-green-800">
            @if (session('status') == 'announcement-created')
                {{ __('Announcement created successfully.') }}
            @elseif (session('status') == 'announcement-updated')
                {{ __('Announcement updated successfully.') }}
            @elseif (session('status') == 'announcement-deleted')
                {{ __('Announcement deleted successfully.') }}
            @else
                {{ session('status') }}
            @endif
        </div>
    @endif

    <div class="mb-6">
        <a href="{{ route('teacher.courses.announcements.create', $course) }}" class="inline-flex items-center justify-center rounded border border-[#6B1212] bg-[#6B1212] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#5A1010]">
            {{ __('New announcement') }}
        </a>
    </div>

    <x-ui.card>
        <div class="divide-y divide-neutral-100">
            @forelse ($announcements as $announcement)
                <div class="py-4 first:pt-0 last:pb-0">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            @if ($announcement->is_pinned)
                                <span class="inline-flex items-center rounded-full bg-[#6B1212]/10 px-2 py-1 text-xs font-medium text-[#6B1212]">
                                    {{ __('Pinned') }}
                                </span>
                            @endif
                            <h3 class="mt-2 font-semibold text-neutral-900">{{ $announcement->title }}</h3>
                            <p class="mt-1 text-sm text-neutral-600">{{ $announcement->user->name }} · {{ $announcement->created_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                            <p class="mt-2 text-sm text-neutral-700 whitespace-pre-line">{{ $announcement->content }}</p>
                            
                            @if ($announcement->replies->count() > 0)
                                <p class="mt-2 text-xs text-neutral-500">{{ $announcement->replies->count() }} {{ __('reply/replies') }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 gap-3">
                            <a href="{{ route('teacher.courses.announcements.edit', [$course, $announcement]) }}" class="text-sm font-medium text-[#6B1212] hover:text-[#5A1010]">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('teacher.courses.announcements.destroy', [$course, $announcement]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this announcement?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-sm text-neutral-500">
                    {{ __('No announcements yet. Create one to communicate with your students.') }}
                </div>
            @endforelse
        </div>
    </x-ui.card>
@endsection
