@extends('layouts.dashboard')

@section('title', __('Teachers'))

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Teachers') }}</h1>
            <p class="mt-1 text-sm text-neutral-600">
                {{ __('Manage teacher accounts for the school.') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('admin.teachers.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-neutral-800"
            >
                {{ __('Add teacher') }}
            </a>
        </div>
    </div>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-100 text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                        <th class="py-3 pe-4">{{ __('Name') }}</th>
                        <th class="py-3 pe-4">{{ __('Email') }}</th>
                        <th class="py-3">{{ __('Created') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($teachers as $teacher)
                        <tr>
                            <td class="py-3 pe-4 text-neutral-800">{{ $teacher->name }}</td>
                            <td class="py-3 pe-4 text-neutral-600">{{ $teacher->email ?? '—' }}</td>
                            <td class="py-3 text-neutral-600">{{ $teacher->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-neutral-500">
                                {{ __('No teachers yet. Add a teacher to get started.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $teachers->links() }}
        </div>
    </x-ui.card>
@endsection
