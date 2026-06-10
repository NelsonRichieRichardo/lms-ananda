<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=merriweather:400,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans text-[#201A17] antialiased">
        <main class="portal-shell min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-6xl items-center justify-center">
                <div class="grid w-full overflow-hidden rounded-[2rem] border border-[#E7DDD1] bg-[#FFFDF9] shadow-2xl shadow-[#6B1212]/10 lg:grid-cols-[1.08fr_0.92fr]">
                    <section class="relative hidden overflow-hidden bg-[#5B1010] px-10 py-12 text-white lg:block">
                        <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-white/8"></div>
                        <div class="absolute -bottom-32 left-8 h-80 w-80 rounded-full border border-white/10"></div>

                        <div class="relative z-10 flex min-h-[34rem] flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-4">
                                    <span class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white p-3 shadow-lg">
                                        <img src="{{ asset('iamges/logo.jpeg') }}" alt="SMA Ananda Batam Logo" class="max-h-full w-auto" />
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/55">{{ __('Official School Portal') }}</p>
                                        <h1 class="portal-heading mt-2 text-3xl font-bold leading-tight">SMA Ananda Batam</h1>
                                    </div>
                                </div>

                                <div class="mt-14 max-w-lg">
                                    <p class="text-sm font-bold uppercase tracking-[0.22em] text-white/50">{{ __('Learning Management System') }}</p>
                                    <h2 class="portal-heading mt-4 text-4xl font-bold leading-tight">{{ __('Academic access for students and teachers.') }}</h2>
                                    <p class="mt-5 text-base leading-7 text-white/72">{{ __('Manage courses, learning materials, assignments, and school academic activity through one secure institutional platform.') }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="rounded-2xl border border-white/10 bg-white/8 p-4">
                                    <p class="text-xs text-white/55">{{ __('Identity') }}</p>
                                    <p class="mt-1 text-sm font-bold">{{ __('School LMS') }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/8 p-4">
                                    <p class="text-xs text-white/55">{{ __('Access') }}</p>
                                    <p class="mt-1 text-sm font-bold">{{ __('Secure') }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/8 p-4">
                                    <p class="text-xs text-white/55">{{ __('Role') }}</p>
                                    <p class="mt-1 text-sm font-bold">{{ __('Academic') }}</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="flex items-center justify-center px-6 py-10 sm:px-10 lg:px-12">
                        <div class="w-full max-w-md">
                            <div class="mb-8 text-center lg:hidden">
                                <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-white p-3 shadow-sm ring-1 ring-[#E7DDD1]">
                                    <img src="{{ asset('iamges/logo.jpeg') }}" alt="SMA Ananda Batam Logo" class="max-h-full w-auto" />
                                </span>
                                <h1 class="portal-heading mt-4 text-2xl font-bold text-[#6B1212]">SMA Ananda Batam</h1>
                            </div>

                            <div class="mb-7">
                                <p class="portal-label">{{ __('Account Access') }}</p>
                                <h2 class="portal-heading mt-2 text-3xl font-bold text-[#201A17]">{{ __('Sign in to LMS') }}</h2>
                                <p class="mt-2 text-sm leading-6 text-[#766A60]">{{ __('Use your registered student or staff ID to continue.') }}</p>
                            </div>

                            {{ $slot }}

                            <div class="mt-8 rounded-2xl border border-[#E7DDD1] bg-[#FAF7F1] p-4">
                                <p class="text-xs leading-5 text-[#766A60]">{{ __('Having trouble signing in? Contact school administration or IT support to verify your account.') }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
