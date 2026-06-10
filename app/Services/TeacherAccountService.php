<?php

namespace App\Services;

use App\Models\User;
use App\Support\RoleName;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TeacherAccountService
{
    /**
     * Create a teacher account with a default password.
     *
     * @param  array{name: string, email: string, password?: string|null}  $data
     */
    public function createTeacher(array $data): User
    {
        $plainPassword = $data['password'] ?? 'password123';

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($plainPassword),
        ]);

        $teacherRole = Role::query()
            ->where('name', RoleName::Teacher)
            ->where('guard_name', 'web')
            ->firstOrFail();

        $user->assignRole($teacherRole);
        $user->forceFill(['role_id' => $teacherRole->id])->save();

        return $user->fresh();
    }
}
