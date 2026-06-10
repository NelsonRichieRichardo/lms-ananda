<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\ModuleProgress;
use App\Notifications\CourseEnrolled;
use App\Services\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly CourseService $courseService
    ) {}

    public function index(): View
    {
        $courses = Course::query()
            ->where('is_published', true)
            ->with('teacher')
            ->withCount('modules', 'assignments')
            ->orderBy('title')
            ->paginate(12);

        return view('student.courses.index', compact('courses'));
    }

    public function show(Request $request, Course $course): View
    {
        $this->authorize('view', $course);

        $course->load(['modules', 'assignments', 'teacher']);
        $course->loadCount('modules', 'assignments');

        $enrollment = $request->user()
            ->enrollments()
            ->where('course_id', $course->id)
            ->first();

        $moduleProgress = ModuleProgress::where('user_id', $request->user()->id)
            ->whereIn('module_id', $course->modules->pluck('id'))
            ->get()
            ->keyBy('module_id');

        $completedModulesCount = $moduleProgress->where('is_completed', true)->count();
        $totalModulesCount = $course->modules->count();
        $completionPercentage = $totalModulesCount > 0 ? round(($completedModulesCount / $totalModulesCount) * 100) : 0;

        return view('student.courses.show', compact('course', 'enrollment', 'moduleProgress', 'completionPercentage', 'totalModulesCount'));
    }

    public function enroll(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('enroll', $course);

        try {
            $this->courseService->enrollStudent($request->user(), $course);
            $request->user()->notify(new CourseEnrolled($course));
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['enroll' => $e->getMessage()]);
        }

        return redirect()
            ->route('student.courses.show', $course)
            ->with('status', __('You are now enrolled in this course.'));
    }

    public function markModuleComplete(Request $request, Course $course, Module $module): RedirectResponse
    {
        $this->authorize('view', $course);

        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $enrollment = $request->user()
            ->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course');
        }

        ModuleProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'module_id' => $module->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

        return back()->with('status', 'module-completed');
    }

    public function markModuleIncomplete(Request $request, Course $course, Module $module): RedirectResponse
    {
        $this->authorize('view', $course);

        if ($module->course_id !== $course->id) {
            abort(404);
        }

        $enrollment = $request->user()
            ->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this course');
        }

        ModuleProgress::where('user_id', $request->user()->id)
            ->where('module_id', $module->id)
            ->update([
                'is_completed' => false,
                'completed_at' => null,
            ]);

        return back()->with('status', 'module-incomplete');
    }
}
