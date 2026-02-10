<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\Domain\AllocationState;
use App\Services\ExaminerAllocation\Domain\SlotType;
use App\Services\ExaminerAllocation\DTO\SlotDTO;

class ShortfallCarryForwardRule implements AllocationRule
{
    public function __construct(
        protected AllocationState $state
    ) {}

    public function applies(array $context): bool
    {
        return true;
    }

    public function process(SlotDTO $slot, array $context): SlotDTO
    {
        // We only track AFTER slot evaluation, so do nothing here
        return $slot;
    }

    /**
     * Called by builder after all slots for a range are processed.
     */
    public function finalizeRange(object $range, array $slots): void
    {
        $seriesKey = $this->state->seriesKey(
            $range->batch_id,
            $range->subject_id,
            $range->centre_id
        );

        $date = $range->from_date;

        $required = array_keys($slots);
        $missing = [];

        foreach ($required as $type) {
            if (
                !isset($slots[$type]) ||
                !$slots[$type]->examinerId ||
                $slots[$type]->examinerId === 0
            ) {
                $missing[] = $type;
            }
        }

        if (!empty($missing)) {
            $this->state->shortfalls[$seriesKey][$date] = $missing;
        }
    }
}
