@extends('layouts.dashboard')

@section('title', __('Students'))

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Students') }}</h1>
            <p class="mt-1 text-sm text-neutral-600">
                {{ __('Accounts are created by the school. Default password is the student’s date of birth as DDMMYYYY.') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('admin.students.import') }}"
                class="inline-flex items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-800 shadow-sm transition hover:border-neutral-300 hover:bg-neutral-50"
            >
                {{ __('Import CSV') }}
            </a>
            <a
                href="{{ route('admin.students.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-neutral-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-neutral-800"
            >
                {{ __('Add student') }}
            </a>
        </div>
    </div>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-100 text-sm">
                <thead>
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                        <th class="py-3 pe-4">{{ __('Student ID') }}</th>
                        <th class="py-3 pe-4">{{ __('Name') }}</th>
                        <th class="py-3 pe-4">{{ __('Date of birth') }}</th>
                        <th class="py-3">{{ __('Email') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse ($students as $student)
                        <tr>
                            <td class="py-3 pe-4 font-mono font-medium text-neutral-900">{{ $student->student_id ?? '—' }}</td>
                            <td class="py-3 pe-4 text-neutral-800">{{ $student->name }}</td>
                            <td class="py-3 pe-4 tabular-nums text-neutral-700">{{ $student->birth_date?->format('Y-m-d') ?? '—' }}</td>
                            <td class="py-3 text-neutral-600">{{ $student->email ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-neutral-500">
                                {{ __('No students yet. Add a student or import a CSV to get started.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </x-ui.card>
@endsection
