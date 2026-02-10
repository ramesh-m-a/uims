<?php

namespace App\Services\ExaminerAllocation\Pickers;

interface ExaminerPicker
{
    public function pick(
        string $slotType,
        int $departmentId,
        int $centreId,
        array $attachedCentreIds,
        array $excludeExaminerIds,
        string $date
    ): ?array;
}
