@extends('layouts.dashboard')

@section('title', __('Import students'))

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.students.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to students') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Bulk import students') }}</h1>
        <p class="mt-1 max-w-2xl text-sm text-neutral-600">
            {{ __('Upload a CSV file or paste rows below. The first line must be a header: name, student_id, birth_date, and optional email. Dates use YYYY-MM-DD.') }}
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-ui.card title="{{ __('Import') }}">
            <form method="POST" action="{{ route('admin.students.import.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="csv_file" :value="__('CSV file (.csv or .txt)')" />
                    <input
                        id="csv_file"
                        name="csv_file"
                        type="file"
                        accept=".csv,.txt,text/csv,text/plain"
                        class="mt-2 block w-full cursor-pointer rounded-xl border border-neutral-200 bg-neutral-50/50 px-3 py-2 text-sm text-neutral-800 file:me-3 file:cursor-pointer file:rounded-lg file:border-0 file:bg-neutral-900 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white hover:file:bg-neutral-800"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('csv_file')" />
                </div>

                <div>
                    <x-input-label for="csv_content" :value="__('Or paste CSV (including header row)')" />
                    <textarea
                        id="csv_content"
                        name="csv_content"
                        rows="12"
                        placeholder="{{ __('name,student_id,birth_date,user@example.com (optional)') }}"
                        class="mt-2 block w-full rounded-xl border border-neutral-200 bg-white px-3 py-2.5 font-mono text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-neutral-400 focus:outline-none focus:ring-2 focus:ring-neutral-900/10"
                    >{{ old('csv_content') }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('csv_content')" />
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <x-primary-button>{{ __('Run import') }}</x-primary-button>
                    <a href="{{ route('admin.students.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</a>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card title="{{ __('Format') }}">
            <p class="text-sm text-neutral-600">{{ __('Example file (UTF-8). One student per line after the header.') }}</p>
            <pre class="mt-4 overflow-x-auto rounded-xl border border-neutral-100 bg-neutral-50 p-4 font-mono text-xs leading-relaxed text-neutral-800">name,student_id,birth_date,email
Ada Example,STU-1001,2010-03-15,
Bob Example,STU-1002,2009-11-02,bob@school.edu</pre>
            <ul class="mt-5 list-inside list-disc space-y-1.5 text-sm text-neutral-600">
                <li>{{ __('student_id must be unique.') }}</li>
                <li>{{ __('birth_date must be in the past; password will be DDMMYYYY.') }}</li>
                <li>{{ __('email is optional; if set, it must be unique.') }}</li>
            </ul>
        </x-ui.card>
    </div>
@endsection
