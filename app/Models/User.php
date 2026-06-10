<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\RoleName;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role_id', 'student_id', 'staff_id', 'birth_date'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(SpatieRole::class, 'role_id');
    }

    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function moduleProgress(): HasMany
    {
        return $this->hasMany(ModuleProgress::class);
    }

    /**
     * Resolve a user by the school login field (student ID or staff ID).
     */
    public static function findBySchoolLogin(string $identifier): ?self
    {
        $trimmed = trim($identifier);

        if ($trimmed === '') {
            return null;
        }

        return self::query()
            ->where(function ($query) use ($trimmed) {
                $query->where('student_id', $trimmed)
                    ->orWhere('staff_id', $trimmed);
            })
            ->first();
    }

    public function dashboardUrl(): string
    {
        if ($this->hasRole(RoleName::SuperAdmin)) {
            return route('admin.dashboard', absolute: false);
        }

        if ($this->hasRole(RoleName::Teacher)) {
            return route('teacher.dashboard', absolute: false);
        }

        return route('student.dashboard', absolute: false);
    }
}
