<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');

        $courses = collect();
        $assignments = collect();
        $students = collect();
        $modules = collect();

        if ($query) {
            if ($type === 'all' || $type === 'courses') {
                $courses = Course::where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->with('teacher')
                    ->where('is_published', true)
                    ->get();
            }

            if ($type === 'all' || $type === 'assignments') {
                $assignments = Assignment::where('title', 'like', "%{$query}%")
                    ->orWhere('instructions', 'like', "%{$query}%")
                    ->whereHas('course', function ($q) {
                        $q->where('is_published', true);
                    })
                    ->with('course')
                    ->get();
            }

            if ($type === 'all' || $type === 'modules') {
                $modules = Module::where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->whereHas('course', function ($q) {
                        $q->where('is_published', true);
                    })
                    ->with('course')
                    ->get();
            }

            if ($type === 'all' || $type === 'students') {
                $students = User::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'student');
                    })
                    ->get();
            }
        }

        return view('search.index', compact('query', 'type', 'courses', 'assignments', 'students', 'modules'));
    }
}
