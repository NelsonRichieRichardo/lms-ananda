<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\SubmissionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssignmentController extends Controller
{
    public function show(Course $course, Assignment $assignment)
    {
        $this->authorize('view', $course);

        $assignment->load('course');

        $submission = $assignment->submissions()->where('user_id', auth()->id())->first();

        return view('student.assignments.show', compact('course', 'assignment', 'submission'));
    }

    public function submit(Request $request, Course $course, Assignment $assignment)
    {
        $this->authorize('view', $course);

        $validated = $request->validate([
            'content' => 'nullable|string|max:65535',
            'attachment' => 'nullable|file|max:20480|mimes:pdf,doc,docx,ppt,pptx,txt,zip,png,jpeg,jpg,gif,webp',
        ]);

        $submission = $assignment->submissions()->where('user_id', auth()->id())->first();

        if ($submission) {
            $submission->history()->create([
                'content' => $submission->content,
                'attachment_path' => $submission->attachment_path,
                'attachment_original_name' => $submission->attachment_original_name,
                'submitted_at' => $submission->submitted_at,
            ]);

            if ($request->hasFile('attachment')) {
                if ($submission->attachment_path) {
                    Storage::disk('public')->delete($submission->attachment_path);
                }
                $file = $request->file('attachment');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('submissions', $filename, 'public');
                $submission->attachment_path = $path;
                $submission->attachment_original_name = $file->getClientOriginalName();
            }

            if (isset($validated['content'])) {
                $submission->content = $validated['content'];
            }

            $submission->submitted_at = now();
            $submission->save();
        } else {
            $path = null;
            $originalName = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('submissions', $filename, 'public');
                $originalName = $file->getClientOriginalName();
            }

            Submission::create([
                'assignment_id' => $assignment->id,
                'user_id' => auth()->id(),
                'content' => $validated['content'] ?? null,
                'attachment_path' => $path,
                'attachment_original_name' => $originalName,
                'submitted_at' => now(),
            ]);
        }

        return redirect()->route('student.courses.assignments.show', [$course, $assignment])
            ->with('status', 'submission-saved');
    }
}
