<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreCourseRequest;
use App\Http\Requests\Teacher\UpdateCourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService
    ) {
        $this->authorizeResource(Course::class, 'course');
    }

    public function index(Request $request): View
    {
        $courses = Course::query()
            ->where('teacher_id', $request->user()->id)
            ->with('teacher')
            ->withCount('modules', 'assignments', 'enrollments')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('teacher.courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('teacher.courses.create');
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        $course = $this->courseService->createForTeacher(
            $request->user(),
            $request->safe()->except(['cover_photo'])->toArray(),
            $request->file('cover_photo')
        );

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Course created successfully.'));
    }

    public function show(Course $course): View
    {
        $course->load(['modules', 'assignments']);
        $course->loadCount('modules', 'assignments', 'enrollments');

        return view('teacher.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        return view('teacher.courses.edit', compact('course'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->courseService->updateCourse(
            $course,
            $request->safe()->except(['cover_photo', 'remove_cover'])->toArray(),
            $request->file('cover_photo'),
            $request->boolean('remove_cover')
        );

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Course updated successfully.'));
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->courseService->deleteCourse($course);

        return redirect()
            ->route('teacher.courses.index')
            ->with('status', __('Course deleted.'));
    }
}
