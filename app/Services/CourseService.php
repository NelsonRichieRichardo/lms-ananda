<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Support\RoleName;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CourseService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createForTeacher(User $teacher, array $data, ?UploadedFile $coverPhoto = null): Course
    {
        $slug = $this->makeUniqueSlug((string) $data['title']);

        $coverPath = null;
        if ($coverPhoto !== null) {
            $coverPath = $coverPhoto->store('covers', 'public');
        }

        return Course::query()->create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'cover_image' => $coverPath,
            'teacher_id' => $teacher->id,
            'is_published' => (bool) ($data['is_published'] ?? false),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateCourse(Course $course, array $data, ?UploadedFile $coverPhoto = null, bool $removeCover = false): Course
    {
        if ($removeCover) {
            $this->deleteStoredPublicPath($course->cover_image);
            $course->cover_image = null;
        } elseif ($coverPhoto !== null) {
            $this->deleteStoredPublicPath($course->cover_image);
            $course->cover_image = $coverPhoto->store('covers', 'public');
        }

        $title = $data['title'] ?? $course->title;
        $slug = $course->slug;

        if (isset($data['title']) && $data['title'] !== $course->title) {
            $slug = $this->makeUniqueSlug((string) $title, $course->id);
        }

        $course->fill([
            'title' => $title,
            'slug' => $slug,
            'description' => $data['description'] ?? $course->description,
            'is_published' => array_key_exists('is_published', $data)
                ? (bool) $data['is_published']
                : $course->is_published,
        ]);

        $course->save();

        return $course->refresh();
    }

    public function deleteCourse(Course $course): void
    {
        $course->load('modules');
        foreach ($course->modules as $module) {
            $module->delete();
        }
        $this->deleteStoredPublicPath($course->cover_image);
        $course->delete();
    }

    public function enrollStudent(User $student, Course $course): Enrollment
    {
        if (! $student->hasRole(RoleName::Student)) {
            throw new InvalidArgumentException('Only students may enroll in courses.');
        }

        if (! $course->is_published) {
            throw new InvalidArgumentException('This course is not available for enrollment.');
        }

        return DB::transaction(function () use ($student, $course) {
            /** @var Enrollment|null $existing */
            $existing = Enrollment::query()
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existing !== null) {
                return $existing;
            }

            return Enrollment::query()->create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'status' => EnrollmentStatus::Active,
            ]);
        });
    }

    public function makeUniqueSlug(string $title, ?int $ignoreCourseId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'course';
        }

        $slug = $base;
        $suffix = 1;

        while (
            Course::query()
                ->where('slug', $slug)
                ->when($ignoreCourseId, fn ($q) => $q->where('id', '!=', $ignoreCourseId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if (! $path || preg_match('#^https?://#i', $path)) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
