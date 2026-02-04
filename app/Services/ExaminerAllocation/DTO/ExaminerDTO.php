<?php

namespace App\Services\ExaminerAllocation\DTO;

final class ExaminerDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $mobile,
        public int $collegeId,
        public int $subjectId,
        public int $typeId,      // internal / external
        public bool $isDummy = false,

        // Allocation tracking
        public array $assignedDates = [],   // ['2026-01-10', '2026-01-11']
        public array $centres = [],         // [12, 14]
        public int $reuseCount = 0,
    ) {}

    public function canAssignOn(string $date): bool
    {
        return !in_array($date, $this->assignedDates, true);
    }
}
