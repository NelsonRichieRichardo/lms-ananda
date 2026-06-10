<?php

namespace Database\Seeders;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use App\Support\RoleName;
use App\Support\SchoolCredentials;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class LmsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::query()->where('name', RoleName::SuperAdmin)->firstOrFail();
        $teacherRole = Role::query()->where('name', RoleName::Teacher)->firstOrFail();
        $studentRole = Role::query()->where('name', RoleName::Student)->firstOrFail();

        $adminBirth = Carbon::parse('1985-01-15');
        $superAdmin = User::query()->create([
            'name' => 'Super Admin',
            'staff_id' => 'SA001',
            'birth_date' => $adminBirth,
            'email' => 'superadmin@school.local',
            'password' => Hash::make(SchoolCredentials::plainPasswordFromBirthDate($adminBirth)),
            'email_verified_at' => now(),
            'role_id' => $superAdminRole->id,
        ]);
        $superAdmin->assignRole($superAdminRole);

        $teacherBirth = Carbon::parse('1990-06-20');
        $teacher = User::query()->create([
            'name' => 'Demo Teacher',
            'staff_id' => 'TCH001',
            'birth_date' => $teacherBirth,
            'email' => 'teacher@school.local',
            'password' => Hash::make(SchoolCredentials::plainPasswordFromBirthDate($teacherBirth)),
            'email_verified_at' => now(),
            'role_id' => $teacherRole->id,
        ]);
        $teacher->assignRole($teacherRole);

        $studentBirth = Carbon::parse('2012-03-15');
        $student = User::query()->create([
            'name' => 'Demo Student',
            'student_id' => 'S2024001',
            'birth_date' => $studentBirth,
            'email' => 'S2024001@school.local',
            'password' => Hash::make(SchoolCredentials::plainPasswordFromBirthDate($studentBirth)),
            'email_verified_at' => now(),
            'role_id' => $studentRole->id,
        ]);
        $student->assignRole($studentRole);

        $alexBirth = Carbon::parse('2011-11-08');
        $extraStudent = User::query()->create([
            'name' => 'Alex Learner',
            'student_id' => 'S2024002',
            'birth_date' => $alexBirth,
            'email' => 'S2024002@school.local',
            'password' => Hash::make(SchoolCredentials::plainPasswordFromBirthDate($alexBirth)),
            'email_verified_at' => now(),
            'role_id' => $studentRole->id,
        ]);
        $extraStudent->assignRole($studentRole);

        $courseA = Course::query()->create([
            'title' => 'Introduction to Algebra',
            'slug' => 'introduction-to-algebra',
            'description' => 'Foundations of algebraic thinking with practical exercises.',
            'cover_image' => null,
            'teacher_id' => $teacher->id,
            'is_published' => true,
        ]);

        $courseB = Course::query()->create([
            'title' => 'Creative Writing Workshop',
            'slug' => 'creative-writing-workshop',
            'description' => 'Develop voice, structure, and revision habits.',
            'cover_image' => null,
            'teacher_id' => $teacher->id,
            'is_published' => true,
        ]);

        Module::query()->insert([
            [
                'course_id' => $courseA->id,
                'title' => 'Variables and Expressions',
                'content' => 'Learn how symbols represent quantities and relationships.',
                'attachment_path' => null,
                'attachment_original_name' => null,
                'order_position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => $courseA->id,
                'title' => 'Linear Equations',
                'content' => 'Solve equations and interpret solutions in context.',
                'attachment_path' => null,
                'attachment_original_name' => null,
                'order_position' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => $courseB->id,
                'title' => 'Finding Ideas',
                'content' => 'Observation, prompts, and journaling techniques.',
                'attachment_path' => null,
                'attachment_original_name' => null,
                'order_position' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $courseA->id,
            'enrolled_at' => now(),
            'status' => EnrollmentStatus::Active,
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $courseB->id,
            'enrolled_at' => now()->subDays(3),
            'status' => EnrollmentStatus::Completed,
        ]);
    }
}
