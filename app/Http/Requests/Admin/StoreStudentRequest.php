<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Support\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleName::SuperAdmin) ?? false;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\Unique>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:64', 'unique:'.User::class.',student_id'],
            'birth_date' => ['required', 'date', 'before:today'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        ];
    }
}
