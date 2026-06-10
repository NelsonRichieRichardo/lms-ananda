<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $events = Event::where('user_id', $user->id)
            ->whereYear('start_at', $year)
            ->whereMonth('start_at', $month)
            ->with(['course', 'assignment'])
            ->orderBy('start_at')
            ->get();

        $assignments = [];
        if ($user->hasRole('student')) {
            $enrolledCourses = $user->enrollments()->pluck('course_id');
            $assignments = Assignment::whereIn('course_id', $enrolledCourses)
                ->whereNotNull('due_at')
                ->whereYear('due_at', $year)
                ->whereMonth('due_at', $month)
                ->with('course')
                ->orderBy('due_at')
                ->get();
        } elseif ($user->hasRole('teacher')) {
            $assignments = Assignment::join('courses', 'assignments.course_id', '=', 'courses.id')
                ->where('courses.teacher_id', $user->id)
                ->whereNotNull('assignments.due_at')
                ->whereYear('assignments.due_at', $year)
                ->whereMonth('assignments.due_at', $month)
                ->select('assignments.*')
                ->with('course')
                ->orderBy('assignments.due_at')
                ->get();
        }

        return view('calendar.index', compact('events', 'assignments', 'year', 'month'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'type' => 'required|in:assignment,exam,meeting,holiday,other',
            'is_all_day' => 'boolean',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if (isset($validated['course_id'])) {
            $course = Course::findOrFail($validated['course_id']);
            $this->authorize('update', $course);
        }

        Event::create([
            'user_id' => auth()->id(),
            'course_id' => $validated['course_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'] ?? null,
            'type' => $validated['type'],
            'is_all_day' => $request->boolean('is_all_day', false),
        ]);

        return redirect()->route('calendar.index')
            ->with('status', __('Event created successfully.'));
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('calendar.index')
            ->with('status', __('Event deleted successfully.'));
    }
}
