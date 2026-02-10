<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\DTO\SlotDTO;
use App\Services\ExaminerAllocation\Domain\AllocationState;
use App\Services\ExaminerAllocation\Pickers\ExaminerPicker;

class ReuseRule implements AllocationRule
{
    public function __construct(
        protected ExaminerPicker $picker,
        protected AllocationState $state,
        protected int $maxReuseDays = 3, // configurable
    ) {}

    public function applies(array $context): bool
    {
        return true;
    }

    public function process(SlotDTO $slot, array $context): SlotDTO
    {
       /* logger()->debug('STATE in ReuseRule', [
            'object_id' => spl_object_id($this->state),
        ]);*/
        $range = $context['range'];

        $seriesKey = $this->state->seriesKey(
            $range->batch_id,
            $range->subject_id,
            $range->centre_id
        );

        $date = $range->from_date;

        // 1. Check if someone was assigned to this slotType previously
        $previousExaminerId = $this->state->lastAssigned($seriesKey, $slot->type);

        if (!$previousExaminerId) {
            return $slot; // nothing to reuse
        }

        // 2. Enforce global date collision
        if ($this->state->usedOnDate($date, $previousExaminerId)) {
            return $slot;
        }

        // 3. Enforce max reuse
        if ($this->state->daysUsed($seriesKey, $previousExaminerId) >= $this->maxReuseDays) {
            return $slot;
        }

        // 4. Try to re-fetch examiner to ensure still eligible
        // 4. Fetch exact examiner by ID (do NOT use picker here)
        $examiner = $this->state->getExaminer($previousExaminerId);

        if (!$examiner) {
            return $slot;
        }

        // 5. Reuse examiner
        return new SlotDTO(
            type: $slot->type,
            examinerId: $previousExaminerId,
            name: $examiner['name'] ?? '',
            mobile: $examiner['mobile'] ?? '',
            status: 'Assigned',
            colour: '#198754',
        );
    }
}
