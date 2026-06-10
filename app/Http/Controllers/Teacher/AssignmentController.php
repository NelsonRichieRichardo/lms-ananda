<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAssignmentRequest;
use App\Http\Requests\Teacher\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Notifications\AssignmentCreated;
use App\Notifications\SubmissionGraded;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function store(StoreAssignmentRequest $request, Course $course): RedirectResponse
    {
        $max = (int) $course->assignments()->max('order_position') ?? 0;

        $assignment = $course->assignments()->create([
            'title' => $request->validated('title'),
            'instructions' => $request->validated('instructions'),
            'due_at' => $request->validated('due_at'),
            'order_position' => $max + 1,
        ]);

        $enrolledStudents = Enrollment::where('course_id', $course->id)->with('user')->get();
        foreach ($enrolledStudents as $enrollment) {
            $enrollment->user->notify(new AssignmentCreated($assignment));
        }

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Assignment added.'));
    }

    public function edit(Course $course, Assignment $assignment): View
    {
        $this->authorize('update', $course);
        abort_unless($assignment->course_id === $course->id, 404);

        return view('teacher.assignments.edit', compact('course', 'assignment'));
    }

    public function update(UpdateAssignmentRequest $request, Course $course, Assignment $assignment): RedirectResponse
    {
        abort_unless($assignment->course_id === $course->id, 404);

        $assignment->update($request->validated());

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Assignment updated.'));
    }

    public function destroy(Course $course, Assignment $assignment): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($assignment->course_id === $course->id, 404);

        $assignment->delete();

        return redirect()
            ->route('teacher.courses.show', $course)
            ->with('status', __('Assignment removed.'));
    }

    public function submissions(Course $course, Assignment $assignment): View
    {
        $this->authorize('update', $course);
        abort_unless($assignment->course_id === $course->id, 404);

        $submissions = $assignment->submissions()->with('user')->get();

        return view('teacher.assignments.submissions', compact('course', 'assignment', 'submissions'));
    }

    public function grade(Request $request, Course $course, Assignment $assignment, Submission $submission): RedirectResponse
    {
        $this->authorize('update', $course);
        abort_unless($assignment->course_id === $course->id, 404);
        abort_unless($submission->assignment_id === $assignment->id, 404);

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('student_id', $submission->user_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Student is not enrolled in this course');
        }

        $validated = $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'grade_comment' => 'nullable|string|max:65535',
        ]);

        $submission->update([
            'grade' => $validated['grade'],
            'grade_comment' => $validated['grade_comment'] ?? null,
            'graded_at' => now(),
        ]);

        $submission->user->notify(new SubmissionGraded($submission));

        return redirect()
            ->route('teacher.courses.assignments.submissions', [$course, $assignment])
            ->with('status', __('Grade saved.'));
    }
}
