<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function index(): View
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->with(['course', 'course.assignments' => function ($query) {
                $query->orderBy('order_position');
            }, 'course.assignments.submissions' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->get();

        $grades = [];
        $courseMetrics = [];

        foreach ($enrollments as $enrollment) {
            $totalAssignments = $enrollment->course->assignments->count();
            $gradedAssignments = 0;
            $totalGrade = 0;

            foreach ($enrollment->course->assignments as $assignment) {
                $submission = $assignment->submissions->first();
                if ($submission && $submission->grade !== null) {
                    $grades[] = [
                        'course' => $enrollment->course,
                        'assignment' => $assignment,
                        'submission' => $submission,
                    ];
                    $gradedAssignments++;
                    $totalGrade += $submission->grade;
                }
            }

            $averageGrade = $gradedAssignments > 0 ? round($totalGrade / $gradedAssignments, 2) : null;
            $completionRate = $totalAssignments > 0 ? round(($gradedAssignments / $totalAssignments) * 100) : 0;

            $courseMetrics[$enrollment->course->id] = [
                'course' => $enrollment->course,
                'total_assignments' => $totalAssignments,
                'graded_assignments' => $gradedAssignments,
                'average_grade' => $averageGrade,
                'completion_rate' => $completionRate,
            ];
        }

        $overallAverage = collect($courseMetrics)->whereNotNull('average_grade')->avg('average_grade');
        $overallAverage = $overallAverage ? round($overallAverage, 2) : null;

        return view('student.gradebook.index', compact('grades', 'courseMetrics', 'overallAverage'));
    }
}
