<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function index(): View
    {
        $courses = Course::where('teacher_id', auth()->id())
            ->with(['assignments' => function ($query) {
                $query->orderBy('order_position');
            }, 'assignments.submissions' => function ($query) {
                $query->with('user');
            }])
            ->withCount('assignments', 'enrollments')
            ->get();

        return view('teacher.gradebook.index', compact('courses'));
    }

    public function show(Course $course): View
    {
        $this->authorize('update', $course);

        $course->load(['assignments' => function ($query) {
            $query->orderBy('order_position');
        }, 'assignments.submissions' => function ($query) {
            $query->with('user');
        }]);

        $course->loadCount('assignments', 'enrollments');

        return view('teacher.gradebook.show', compact('course'));
    }
}
