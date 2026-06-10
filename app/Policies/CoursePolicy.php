<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Support\RoleName;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([RoleName::Teacher, RoleName::Student, RoleName::SuperAdmin]);
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->hasRole(RoleName::Teacher) && $user->id === $course->teacher_id) {
            return true;
        }

        if ($user->hasRole(RoleName::Student) && $course->is_published) {
            return true;
        }

        return $user->hasRole(RoleName::SuperAdmin);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::Teacher);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasRole(RoleName::Teacher) && $user->id === $course->teacher_id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $this->update($user, $course);
    }

    public function enroll(User $user, Course $course): bool
    {
        return $user->hasRole(RoleName::Student) && $course->is_published;
    }
}
