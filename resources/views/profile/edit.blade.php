@extends('layouts.dashboard')

@section('title', __('Account'))

@section('page_heading', __('Account & profile'))

@section('sidebar')
    @include('partials.sidebar-app')
@endsection

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <x-ui.card>
            @include('profile.partials.update-profile-information-form')
        </x-ui.card>

        <x-ui.card>
            @include('profile.partials.update-password-form')
        </x-ui.card>

        <x-ui.card>
            @include('profile.partials.delete-user-form')
        </x-ui.card>
    </div>
@endsection
