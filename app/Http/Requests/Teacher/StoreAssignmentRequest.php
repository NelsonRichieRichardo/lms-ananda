<?php

namespace App\Http\Requests\Teacher;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('as_title')) {
            return;
        }

        $due = $this->input('as_due_at');

        $this->merge([
            'title' => $this->input('as_title'),
            'instructions' => $this->input('as_instructions'),
            'due_at' => ($due === '' || $due === null) ? null : $due,
        ]);
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
