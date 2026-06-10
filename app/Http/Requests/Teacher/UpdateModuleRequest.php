<?php

namespace App\Http\Requests\Teacher;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'remove_material_attachment' => $this->boolean('remove_material_attachment'),
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
            'content' => ['nullable', 'string', 'max:65535'],
            'mat_attachment' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,ppt,pptx,txt,zip,png,jpeg,jpg,gif,webp'],
            'remove_material_attachment' => ['sometimes', 'boolean'],
        ];
    }
}
