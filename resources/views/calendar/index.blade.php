@extends('layouts.dashboard')

@section('title', __('Calendar'))

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
        <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Calendar') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">{{ __('View your events and assignment deadlines') }}</p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr,21rem]">
        <div class="space-y-6">
            <x-ui.card>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900">{{ now()->create($year . '-' . $month . '-01')->format('F Y') }}</h2>
                    <div class="flex gap-2">
                        <a href="{{ route('calendar.index', ['year' => $year, 'month' => $month - 1]) }}" class="rounded-lg border border-neutral-200 px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">{{ __('Previous') }}</a>
                        <a href="{{ route('calendar.index', ['year' => $year, 'month' => $month + 1]) }}" class="rounded-lg border border-neutral-200 px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50">{{ __('Next') }}</a>
                    </div>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-center">
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Sun') }}</div>
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Mon') }}</div>
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Tue') }}</div>
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Wed') }}</div>
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Thu') }}</div>
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Fri') }}</div>
                    <div class="py-2 text-xs font-semibold text-neutral-500">{{ __('Sat') }}</div>
                    
                    @php
                        $date = now()->create($year . '-' . $month . '-01');
                        $startDay = $date->dayOfWeek;
                        $daysInMonth = $date->daysInMonth;
                        $today = now();
                    @endphp
                    
                    @for ($i = 0; $i < $startDay; $i++)
                        <div class="h-24 rounded-lg border border-neutral-100 bg-neutral-50"></div>
                    @endfor
                    
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $currentDate = now()->create($year . '-' . $month . '-' . $day);
                            $dayEvents = $events->filter(function ($event) use ($currentDate) {
                                return $event->start_at->isSameDay($currentDate);
                            });
                            $dayAssignments = $assignments->filter(function ($assignment) use ($currentDate) {
                                return $assignment->due_at->isSameDay($currentDate);
                            });
                        @endphp
                        <div class="h-24 rounded-lg border border-neutral-100 p-2 @if ($currentDate->isSameDay($today)) bg-[#6B1212]/5 @else bg-white @endif">
                            <span class="text-sm font-medium @if ($currentDate->isSameDay($today)) text-[#6B1212] @else text-neutral-700 @endif">{{ $day }}</span>
                            
                            @if ($dayAssignments->count() > 0)
                                @foreach ($dayAssignments as $assignment)
                                    <div class="mt-1 truncate rounded bg-[#6B1212]/10 px-1 py-0.5 text-xs font-medium text-[#6B1212]">
                                        {{ $assignment->title }}
                                    </div>
                                @endforeach
                            @endif
                            
                            @if ($dayEvents->count() > 0)
                                @foreach ($dayEvents as $event)
                                    <div class="mt-1 truncate rounded bg-neutral-100 px-1 py-0.5 text-xs font-medium text-neutral-700">
                                        {{ $event->title }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endfor
                </div>
            </x-ui.card>
        </div>

        <aside class="space-y-6">
            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-neutral-900">{{ __('Add Event') }}</h3>
                <form method="POST" action="{{ route('calendar.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="title" :value="__('Title')" />
                        <input
                            id="title"
                            name="title"
                            type="text"
                            required
                            class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>
                    <div>
                        <x-input-label for="start_at" :value="__('Start Date & Time')" />
                        <input
                            id="start_at"
                            name="start_at"
                            type="datetime-local"
                            required
                            class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('start_at')" />
                    </div>
                    <div>
                        <x-input-label for="end_at" :value="__('End Date & Time (optional)')" />
                        <input
                            id="end_at"
                            name="end_at"
                            type="datetime-local"
                            class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('end_at')" />
                    </div>
                    <div>
                        <x-input-label for="type" :value="__('Type')" />
                        <select
                            id="type"
                            name="type"
                            required
                            class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                        >
                            <option value="other">{{ __('Other') }}</option>
                            <option value="meeting">{{ __('Meeting') }}</option>
                            <option value="exam">{{ __('Exam') }}</option>
                            <option value="holiday">{{ __('Holiday') }}</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('type')" />
                    </div>
                    <div>
                        <x-input-label for="description" :value="__('Description (optional)')" />
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="mt-2 block w-full rounded border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                        ></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>
                    <x-primary-button>{{ __('Add Event') }}</x-primary-button>
                </form>
            </x-ui.card>

            <x-ui.card>
                <h3 class="mb-4 text-lg font-semibold text-neutral-900">{{ __('Upcoming Deadlines') }}</h3>
                @forelse ($assignments->take(5) as $assignment)
                    <div class="mb-3 last:mb-0">
                        <p class="font-medium text-neutral-900">{{ $assignment->title }}</p>
                        <p class="text-sm text-neutral-600">{{ $assignment->course->title }}</p>
                        <p class="text-xs text-[#6B1212]">{{ __('Due') }} {{ $assignment->due_at->timezone(config('app.timezone'))->format('M j, Y g:i A') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-neutral-500">{{ __('No upcoming deadlines') }}</p>
                @endforelse
            </x-ui.card>
        </aside>
    </div>
@endsection
