<?php

namespace App\DataTransferObjects;

final class BulkStudentImportResult
{
    /**
     * @param  list<array{line: int, message: string}>  $errors
     */
    public function __construct(
        public int $created = 0,
        public array $errors = [],
    ) {}

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }
}
