<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\Domain\SlotType;

class SchemeRule
{
    /**
     * Scheme IDs that require External-O
     * Adjust if your mapping changes.
     */
    protected array $schemeWithExternalO = [1, 2, 3, 4];

    /**
     * Returns allowed slot types for this range.
     */
    public function slotsFor(object $range): array
    {
        // Default slots
        $slots = [
            SlotType::INTERNAL_C,
            SlotType::INTERNAL_A,
            SlotType::EXTERNAL,
        ];

        // Scheme allows External-O
        if (in_array((int)$range->scheme_id, $this->schemeWithExternalO, true)) {
            $slots[] = SlotType::EXTERNAL_O;
        }

        return $slots;
    }
}
