<?php

namespace App\Http\Requests\Teacher;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssignmentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('due_at') && $this->input('due_at') === '') {
            $this->merge(['due_at' => null]);
        }
    }

    public function authorize(): bool
    {
        /** @var Course $course */
        $course = $this->route('course');

        return $this->user()?->can('update', $course) ?? false;
    }

    /**
     * @return array<string, array<int, string|object>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:65535'],
            'due_at' => ['nullable', 'date'],
        ];
    }
}
