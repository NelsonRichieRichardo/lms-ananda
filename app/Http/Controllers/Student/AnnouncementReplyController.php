<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementReply;
use App\Models\Course;
use Illuminate\Http\Request;

class AnnouncementReplyController extends Controller
{
    public function store(Request $request, Course $course, Announcement $announcement)
    {
        $this->authorize('view', $course);

        if ($announcement->course_id && $announcement->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $announcement->replies()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return back()->with('status', 'reply-saved');
    }

    public function destroy(Request $request, Course $course, Announcement $announcement, AnnouncementReply $reply)
    {
        $this->authorize('view', $course);

        if ($reply->user_id !== $request->user()->id) {
            abort(403);
        }

        $reply->delete();

        return back()->with('status', 'reply-deleted');
    }
}
