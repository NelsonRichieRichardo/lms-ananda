@extends('layouts.dashboard')

@section('title', __('Add student'))

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.students.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to students') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Add student') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">
            {{ __('The student will log in with their Student ID. Initial password is their date of birth as eight digits (DDMMYYYY).') }}
        </p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Full name')" />
                <x-text-input id="name" class="mt-2" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="student_id" :value="__('Student ID')" />
                <x-text-input id="student_id" class="mt-2" type="text" name="student_id" :value="old('student_id')" required autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
            </div>

            <div>
                <x-input-label for="birth_date" :value="__('Date of birth')" />
                <x-text-input id="birth_date" class="mt-2" type="date" name="birth_date" :value="old('birth_date')" required />
                <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email (optional)')" />
                <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email')" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Create account') }}</x-primary-button>
                <a href="{{ route('admin.students.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </x-ui.card>
@endsection
