@extends('layouts.dashboard')

@section('title', __('Add teacher'))

@section('sidebar')
    @include('partials.sidebar-admin')
@endsection

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.teachers.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('← Back to teachers') }}</a>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-neutral-900">{{ __('Add teacher') }}</h1>
        <p class="mt-1 text-sm text-neutral-600">
            {{ __('The teacher will log in with their email. Default password is "password123" unless specified.') }}
        </p>
    </div>

    <x-ui.card class="max-w-2xl">
        <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-6">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Full name')" />
                <x-text-input id="name" class="mt-2" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-2" type="email" name="email" :value="old('email')" required autocomplete="email" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password (optional)')" />
                <x-text-input id="password" class="mt-2" type="password" name="password" :value="old('password')" autocomplete="new-password" />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                <p class="mt-1 text-xs text-neutral-500">{{ __('Leave blank to use default password: password123') }}</p>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Create account') }}</x-primary-button>
                <a href="{{ route('admin.teachers.index') }}" class="text-sm font-medium text-neutral-600 hover:text-neutral-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </x-ui.card>
@endsection
