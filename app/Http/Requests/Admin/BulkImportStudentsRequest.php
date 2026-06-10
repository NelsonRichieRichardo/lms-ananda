<?php

namespace App\Http\Requests\Admin;

use App\Support\RoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BulkImportStudentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleName::SuperAdmin) ?? false;
    }

    /**
     * @return array<string, array<int, string|object>|string>
     */
    public function rules(): array
    {
        return [
            'csv_content' => ['nullable', 'string', 'max:524288'],
            'csv_file' => ['nullable', 'file', 'mimes:csv,txt', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $hasFile = $this->hasFile('csv_file');
            $content = trim((string) $this->input('csv_content', ''));
            if (! $hasFile && $content === '') {
                $v->errors()->add('csv_content', __('Provide pasted CSV text or upload a .csv file.'));
            }
        });
    }

    public function csvPayload(): string
    {
        if ($this->hasFile('csv_file')) {
            $raw = file_get_contents($this->file('csv_file')->getRealPath());

            return is_string($raw) ? $raw : '';
        }

        return (string) $this->input('csv_content', '');
    }
}
