<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'LMS'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=merriweather:400,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans antialiased">
        <div
            x-data="{ sidebarOpen: false }"
            class="portal-shell flex min-h-screen overflow-x-hidden"
            @keydown.escape.window="sidebarOpen = false"
        >
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-[#201A17]/55 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-[min(88vw,19rem)] shrink-0 -translate-x-full flex-col overflow-hidden border-r border-white/10 bg-[#5B1010] text-white shadow-2xl transition-transform duration-200 ease-out lg:sticky lg:top-0 lg:z-0 lg:h-screen lg:w-72 lg:translate-x-0"
                :class="sidebarOpen ? '!translate-x-0' : ''"
            >
                <div class="border-b border-white/10 px-5 py-5">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ Auth::user()->dashboardUrl() }}" class="flex min-w-0 items-center gap-3" @click="sidebarOpen = false">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white p-2 shadow-sm">
                                <img src="{{ asset('iamges/logo.jpeg') }}" alt="SMA Ananda Batam Logo" class="max-h-full w-auto" />
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold leading-tight">SMA Ananda Batam</span>
                                <span class="mt-0.5 block text-xs text-white/65">Learning Management System</span>
                            </span>
                        </a>
                        <button type="button" class="rounded-xl p-2 text-white/75 hover:bg-white/10 lg:hidden" @click="sidebarOpen = false" aria-label="{{ __('Close menu') }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <nav class="flex-1 overflow-y-auto px-4 py-5" @click.capture="if ($event.target.closest('a')) { sidebarOpen = false }">
                    <div class="space-y-5">
                        @yield('sidebar')
                    </div>
                </nav>

                <div class="border-t border-white/10 p-4">
                    <div class="rounded-2xl bg-white/8 p-3">
                        <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                        @if (Auth::user()->student_id)
                            <p class="mt-1 truncate text-xs text-white/60">{{ __('Student ID') }} · {{ Auth::user()->student_id }}</p>
                        @elseif (Auth::user()->staff_id)
                            <p class="mt-1 truncate text-xs text-white/60">{{ __('Staff ID') }} · {{ Auth::user()->staff_id }}</p>
                        @else
                            <p class="mt-1 truncate text-xs text-white/60">{{ __('School Account') }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl border border-white/10 px-3 py-2.5 text-sm font-semibold text-white/85 transition hover:bg-white/10 hover:text-white">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                            {{ __('Log out') }}
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex min-h-screen min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 border-b border-[#E7DDD1]/80 bg-[#FFFDF9]/85 backdrop-blur">
                    <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
                        <button type="button" class="rounded-xl border border-[#E7DDD1] bg-white p-2 text-[#201A17] shadow-sm lg:hidden" @click="sidebarOpen = true" aria-label="{{ __('Open navigation') }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        </button>

                        <div class="min-w-0 flex-1">
                            @hasSection('page_heading')
                                <h1 class="truncate text-sm font-bold text-[#201A17] sm:text-base">@yield('page_heading')</h1>
                            @else
                                <p class="truncate text-sm font-bold text-[#201A17]">SMA Ananda Batam LMS</p>
                            @endif
                            <p class="hidden text-xs text-[#766A60] sm:block">{{ now()->format('l, d F Y') }}</p>
                        </div>

                        <div class="hidden items-center gap-3 lg:flex">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-[#201A17]">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-[#766A60]">{{ __('Academic Portal') }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#6B1212] text-sm font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 py-7 sm:px-6 lg:px-8">
                    <div class="mx-auto w-full max-w-7xl">
                        @if (session('status'))
                            @php
                                $rawStatus = session('status');
                                $statusText = match ($rawStatus) {
                                    'profile-updated', 'password-updated' => __('Saved.'),
                                    'verification-link-sent' => __('A new verification link has been sent to your email address.'),
                                    default => $rawStatus,
                                };
                            @endphp
                            <x-ui.alert type="success">{{ $statusText }}</x-ui.alert>
                        @endif

                        @if ($errors->any())
                            <x-ui.alert type="danger">{{ $errors->first() }}</x-ui.alert>
                        @endif

                        @if (session('import_errors') && count(session('import_errors')) > 0)
                            <x-ui.alert type="warning" class="mb-4">
                                <p class="font-medium text-gray-900">{{ __('Some rows were skipped') }}</p>
                                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-gray-700">
                                    @foreach (session('import_errors') as $err)
                                        <li>{{ __('Line :line: :msg', ['line' => $err['line'], 'msg' => $err['message']]) }}</li>
                                    @endforeach
                                </ul>
                            </x-ui.alert>
                        @endif

                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
