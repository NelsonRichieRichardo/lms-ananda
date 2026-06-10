@extends('layouts.dashboard')

@section('title', __('Courses'))

@section('sidebar')
    @include('partials.sidebar-student')
@endsection

@section('page_heading', __('Courses'))

@section('content')
    <section class="mb-7 rounded-[1.7rem] border border-[#E7DDD1] bg-[#FFFDF9] p-6 shadow-sm sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="portal-label">{{ __('Course Catalog') }}</p>
                <h1 class="portal-heading mt-2 text-3xl font-bold text-[#201A17]">{{ __('My learning courses') }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-[#766A60]">{{ __('Browse published courses, review your teacher’s materials, and continue your academic activity.') }}</p>
            </div>
            <div class="rounded-2xl bg-[#FAF7F1] px-4 py-3 text-sm font-semibold text-[#6B1212]">
                {{ trans_choice('{0} No course listed|{1} :count course listed|[2,*] :count courses listed', $courses->total(), ['count' => $courses->total()]) }}
            </div>
        </div>
    </section>

    <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($courses as $course)
            <article class="portal-card group flex min-h-[17rem] flex-col overflow-hidden rounded-[1.5rem] transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#6B1212]/10">
                <div class="h-2 bg-[#6B1212]"></div>
                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <span class="portal-badge">{{ __('Published') }}</span>
                        <span class="rounded-full bg-[#FAF7F1] px-3 py-1 text-xs font-bold text-[#766A60]">{{ __('Course') }}</span>
                    </div>

                    <h2 class="portal-heading mt-5 text-xl font-bold leading-snug text-[#201A17]">{{ $course->title }}</h2>
                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-[#766A60]">{{ \Illuminate\Support\Str::limit($course->description ?: __('No description provided.'), 135) }}</p>

                    <div class="mt-5 rounded-2xl bg-[#FAF7F1] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#766A60]">{{ __('Teacher') }}</p>
                        <p class="mt-1 text-sm font-bold text-[#201A17]">{{ $course->teacher?->name ?? __('Unassigned') }}</p>
                    </div>

                    <div class="mt-auto pt-5">
                        <a href="{{ route('student.courses.show', $course) }}" class="portal-button-primary w-full">
                            {{ __('Open classroom') }}
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-[1.5rem] border border-dashed border-[#D7CABB] bg-[#FFFDF9] p-12 text-center">
                <p class="portal-heading text-2xl font-bold text-[#201A17]">{{ __('No courses available yet') }}</p>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#766A60]">{{ __('Published courses will appear here after your teacher or school administrator makes them available.') }}</p>
            </div>
        @endforelse
    </section>

    <div class="mt-8">
        {{ $courses->links() }}
    </div>
@endsection
