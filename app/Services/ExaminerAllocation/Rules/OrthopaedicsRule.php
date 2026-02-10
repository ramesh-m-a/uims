<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\Domain\SlotType;

class OrthopaedicsRule
{
    /**
     * Your department id for Orthopaedics
     * You used 504 in legacy.
     */
    protected int $orthoDepartmentId = 504;

    /**
     * Override slots if Orthopaedics.
     */
    public function slotsFor(object $range): ?array
    {
        if ((int)$range->department_id !== $this->orthoDepartmentId) {
            return null;
        }

        // Ortho rule: only 2 slots
        return [
            SlotType::INTERNAL_C,
            SlotType::EXTERNAL,
        ];
    }
}
