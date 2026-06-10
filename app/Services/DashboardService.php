<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Support\RoleName;

class DashboardService
{
    /**
     * @return array<string, int|float>
     */
    public function superAdminStats(): array
    {
        return [
            'users_count' => User::query()->count(),
            'teachers_count' => User::query()->role(RoleName::Teacher)->count(),
            'students_count' => User::query()->role(RoleName::Student)->count(),
            'courses_count' => Course::query()->count(),
            'enrollments_count' => Enrollment::query()->count(),
            'published_courses_count' => Course::query()->where('is_published', true)->count(),
        ];
    }

    /**
     * @return array<string, int|float>
     */
    public function teacherStats(User $teacher): array
    {
        $courseIds = Course::query()->where('teacher_id', $teacher->id)->pluck('id');

        $activeEnrollments = Enrollment::query()
            ->whereIn('course_id', $courseIds)
            ->where('status', EnrollmentStatus::Active)
            ->count();

        $completedEnrollments = Enrollment::query()
            ->whereIn('course_id', $courseIds)
            ->where('status', EnrollmentStatus::Completed)
            ->count();

        // Get pending submissions count
        $pendingSubmissions = \App\Models\Submission::query()
            ->whereHas('assignment', function ($query) use ($courseIds) {
                $query->whereIn('course_id', $courseIds);
            })
            ->whereNull('grade')
            ->count();

        return [
            'courses_count' => Course::query()->where('teacher_id', $teacher->id)->count(),
            'published_courses' => Course::query()
                ->where('teacher_id', $teacher->id)
                ->where('is_published', true)
                ->count(),
            'active_enrollments' => $activeEnrollments,
            'completed_enrollments' => $completedEnrollments,
            'pending_submissions' => $pendingSubmissions,
        ];
    }

    /**
     * @return array<string, int|float|list<array<string, mixed>>>
     */
    public function studentStats(User $student): array
    {
        $enrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->with('course.modules', 'course.assignments')
            ->get();

        $active = $enrollments->where('status', EnrollmentStatus::Active)->count();
        $completed = $enrollments->where('status', EnrollmentStatus::Completed)->count();

        $progressRows = $enrollments->map(function (Enrollment $enrollment) {
            $course = $enrollment->course;
            $moduleCount = $course?->modules->count() ?? 0;
            $assignmentCount = $course?->assignments->count() ?? 0;

            // Calculate progress based on modules and assignments
            $totalItems = $moduleCount + $assignmentCount;
            $completedItems = 0;

            // For now, progress is based on module count (can be enhanced with completion tracking later)
            $progress = $totalItems > 0 ? round(($moduleCount / $totalItems) * 100, 0) : 0;

            return [
                'course_id' => $course?->id,
                'course_title' => $course?->title ?? '',
                'status' => $enrollment->status->value,
                'module_count' => $moduleCount,
                'assignment_count' => $assignmentCount,
                'progress' => $progress,
                'enrolled_at' => $enrollment->enrolled_at,
            ];
        })->values()->all();

        // Get upcoming assignments - optimized with join instead of nested whereHas
        $upcomingAssignments = \App\Models\Assignment::query()
            ->join('courses', 'assignments.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) use ($student) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                     ->where('enrollments.student_id', $student->id);
            })
            ->where('assignments.due_at', '>=', now())
            ->orderBy('assignments.due_at')
            ->select('assignments.*')
            ->take(5)
            ->with('course')
            ->get();

        // Get recent materials - optimized with join instead of nested whereHas
        $recentMaterials = \App\Models\Module::query()
            ->join('courses', 'modules.course_id', '=', 'courses.id')
            ->join('enrollments', function ($join) use ($student) {
                $join->on('courses.id', '=', 'enrollments.course_id')
                     ->where('enrollments.student_id', $student->id);
            })
            ->orderBy('modules.created_at', 'desc')
            ->select('modules.*')
            ->take(5)
            ->with('course')
            ->get();

        // Get announcements - optimized with join instead of nested whereHas
        $enrolledCourseIds = $enrollments->pluck('course_id')->filter()->toArray();
        $announcements = \App\Models\Announcement::query()
            ->where(function ($query) use ($enrolledCourseIds) {
                $query->whereNull('course_id')
                    ->orWhereIn('course_id', $enrolledCourseIds);
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->with('user', 'course')
            ->get();

        $avgModulesPerCourse = $enrollments->isEmpty()
            ? 0.0
            : round(
                $enrollments->avg(fn (Enrollment $e) => $e->course?->modules->count() ?? 0) ?? 0,
                1
            );

        return [
            'enrolled_courses' => $enrollments->count(),
            'active_enrollments' => $active,
            'completed_enrollments' => $completed,
            'avg_modules_per_course' => $avgModulesPerCourse,
            'progress_rows' => $progressRows,
            'upcoming_assignments' => $upcomingAssignments,
            'recent_materials' => $recentMaterials,
            'announcements' => $announcements,
            'unread_notifications_count' => $student->unreadNotifications()->count(),
            'recent_notifications' => $student->notifications()->latest()->take(3)->get(),
            'total_notifications_count' => $student->notifications()->count(),
        ];
    }
}
