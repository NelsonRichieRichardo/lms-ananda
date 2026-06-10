<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorize('update', $course);

        $announcements = $course->announcements()
            ->with('user')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.courses.announcements.index', compact('course', 'announcements'));
    }

    public function create(Course $course): View
    {
        $this->authorize('update', $course);

        return view('teacher.courses.announcements.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $course->announcements()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('teacher.courses.announcements.index', $course)
            ->with('status', 'announcement-created');
    }

    public function edit(Course $course, Announcement $announcement): View
    {
        $this->authorize('update', $course);

        if ($announcement->course_id !== $course->id) {
            abort(404);
        }

        return view('teacher.courses.announcements.edit', compact('course', 'announcement'));
    }

    public function update(Request $request, Course $course, Announcement $announcement)
    {
        $this->authorize('update', $course);

        if ($announcement->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $announcement->update($validated);

        return redirect()
            ->route('teacher.courses.announcements.index', $course)
            ->with('status', 'announcement-updated');
    }

    public function destroy(Course $course, Announcement $announcement)
    {
        $this->authorize('update', $course);

        if ($announcement->course_id !== $course->id) {
            abort(404);
        }

        $announcement->delete();

        return redirect()
            ->route('teacher.courses.announcements.index', $course)
            ->with('status', 'announcement-deleted');
    }
}
