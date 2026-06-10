<?php

namespace App\Services;

use App\DataTransferObjects\BulkStudentImportResult;
use App\Models\User;
use App\Support\RoleName;
use App\Support\SchoolCredentials;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StudentAccountService
{
    /**
     * Create a student account with default password = date of birth (DDMMYYYY).
     *
     * @param  array{name: string, student_id: string, birth_date: string|\DateTimeInterface, email?: string|null}  $data
     */
    public function createStudent(array $data): User
    {
        $birth = Carbon::parse($data['birth_date'])->startOfDay();
        $plainPassword = SchoolCredentials::plainPasswordFromBirthDate($birth);

        $email = isset($data['email']) && $data['email'] !== ''
            ? $data['email']
            : null;

        $user = User::query()->create([
            'name' => $data['name'],
            'student_id' => $data['student_id'],
            'birth_date' => $birth,
            'email' => $email,
            'password' => Hash::make($plainPassword),
        ]);

        $studentRole = Role::query()
            ->where('name', RoleName::Student)
            ->where('guard_name', 'web')
            ->firstOrFail();

        $user->assignRole($studentRole);
        $user->forceFill(['role_id' => $studentRole->id])->save();

        return $user->fresh();
    }

    /**
     * Import students from CSV. First row must be the header:
     * name,student_id,birth_date,email — email is optional (may be empty).
     * birth_date: YYYY-MM-DD recommended; ISO dates are accepted.
     *
     * @return BulkStudentImportResult
     */
    public function importFromCsvContent(string $raw): BulkStudentImportResult
    {
        $result = new BulkStudentImportResult;

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        if (! is_array($lines)) {
            $result->errors[] = ['line' => 0, 'message' => __('Could not read CSV.')];

            return $result;
        }

        $lines = array_values(array_filter(array_map('trim', $lines), fn (string $l) => $l !== ''));
        if ($lines === []) {
            $result->errors[] = ['line' => 0, 'message' => __('CSV is empty.')];

            return $result;
        }

        $header = array_map('trim', str_getcsv(array_shift($lines)));
        $normalizedHeader = array_map(fn (string $h) => strtolower($h), $header);

        $idxName = array_search('name', $normalizedHeader, true);
        $idxStudentId = array_search('student_id', $normalizedHeader, true);
        $idxBirth = array_search('birth_date', $normalizedHeader, true);
        $idxEmail = array_search('email', $normalizedHeader, true);

        if ($idxName === false || $idxStudentId === false || $idxBirth === false) {
            $result->errors[] = [
                'line' => 1,
                'message' => __('Header must include columns: name, student_id, birth_date (optional: email).'),
            ];

            return $result;
        }

        $lineNumber = 1;

        foreach ($lines as $row) {
            $lineNumber++;
            $cells = str_getcsv($row);
            $name = trim((string) ($cells[$idxName] ?? ''));
            $studentId = trim((string) ($cells[$idxStudentId] ?? ''));
            $birthRaw = trim((string) ($cells[$idxBirth] ?? ''));
            $email = $idxEmail !== false ? trim((string) ($cells[$idxEmail] ?? '')) : '';

            if ($name === '' && $studentId === '' && $birthRaw === '') {
                continue;
            }

            if ($name === '' || $studentId === '' || $birthRaw === '') {
                $result->errors[] = [
                    'line' => $lineNumber,
                    'message' => __('Each row needs name, student_id, and birth_date.'),
                ];

                continue;
            }

            if (User::query()->where('student_id', $studentId)->exists()) {
                $result->errors[] = [
                    'line' => $lineNumber,
                    'message' => __('Student ID :id already exists.', ['id' => $studentId]),
                ];

                continue;
            }

            try {
                $birth = Carbon::parse($birthRaw)->startOfDay();
            } catch (\Throwable) {
                $result->errors[] = [
                    'line' => $lineNumber,
                    'message' => __('Invalid birth_date for :id.', ['id' => $studentId]),
                ];

                continue;
            }

            if ($birth->isFuture() || $birth->isToday()) {
                $result->errors[] = [
                    'line' => $lineNumber,
                    'message' => __('birth_date must be before today (:id).', ['id' => $studentId]),
                ];

                continue;
            }

            $emailValue = $email !== '' ? strtolower($email) : null;
            if ($emailValue !== null && User::query()->where('email', $emailValue)->exists()) {
                $result->errors[] = [
                    'line' => $lineNumber,
                    'message' => __('Email already in use for :id.', ['id' => $studentId]),
                ];

                continue;
            }

            try {
                DB::transaction(function () use ($name, $studentId, $birth, $emailValue): void {
                    $this->createStudent([
                        'name' => $name,
                        'student_id' => $studentId,
                        'birth_date' => $birth,
                        'email' => $emailValue,
                    ]);
                });
                $result->created++;
            } catch (\Throwable $e) {
                $result->errors[] = [
                    'line' => $lineNumber,
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $result;
    }
}
