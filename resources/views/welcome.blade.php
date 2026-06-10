<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'LMS') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: "DM Sans", ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="min-h-screen bg-[#fafafa] px-6 py-16 text-neutral-900 antialiased">
        <div class="mx-auto flex max-w-lg flex-col items-center text-center">
            <p class="text-sm font-medium text-neutral-500">{{ config('app.name', 'LMS') }}</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">{{ __('Learning management') }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-neutral-600">
                {{ __('Sign in with your school Student ID or Staff ID to access courses and materials.') }}
            </p>
            @if (Route::has('login'))
                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-flex rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-neutral-800"
                        >
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex rounded-xl bg-neutral-900 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-neutral-800"
                        >
                            {{ __('Log in') }}
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </body>
</html>
