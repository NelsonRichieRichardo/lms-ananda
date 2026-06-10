<?php

namespace App\Http\Requests\Teacher;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreModuleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('mat_title')) {
            $this->merge([
                'title' => $this->input('mat_title'),
                'content' => $this->input('mat_content'),
            ]);
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
            'content' => ['nullable', 'string', 'max:65535'],
            'mat_attachment' => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,ppt,pptx,txt,zip,png,jpeg,jpg,gif,webp'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            if ($this->hasFile('mat_attachment')) {
                return;
            }
            $text = trim((string) $this->input('mat_content', ''));
            if ($text === '') {
                $v->errors()->add('mat_content', __('Add text in the content box or attach a file.'));
            }
        });
    }
}
