<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\DTO\SlotDTO;
use App\Services\ExaminerAllocation\Domain\AllocationState;

class ClonePreferenceRule implements AllocationRule
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
        $range = $context['range'];
        $date  = $range->from_date;

        // If already filled, never touch
        if ($slot->examinerId) {
            return $slot;
        }

        $seriesKey = $this->state->seriesKey(
            $range->batch_id,
            $range->subject_id,
            $range->centre_id
        );

        // No series → nothing to clone
        if (!$this->state->hasSeries($seriesKey)) {
            return $slot;
        }

        $dates = $this->state->getSeriesDates($seriesKey);
        sort($dates);

        $firstDate = $dates[0] ?? null;

        // First day or invalid → do not clone
        if (!$firstDate || $firstDate === $date) {
            return $slot;
        }

        // Fetch exactly what was assigned on Day-1 for this slot
        $assigned = $this->state->getSlotAssignments(
            $seriesKey,
            $firstDate,
            $slot->type
        );

        // CRITICAL FIX: must be real examiner
        $day1ExaminerId = $assigned[0] ?? null;
        if (!$day1ExaminerId || $day1ExaminerId <= 0) {
            return $slot;   // 🚫 do NOT fabricate assignments
        }

        // Prevent double use on same date
        if ($this->state->usedOnDate($date, $day1ExaminerId)) {
            return $slot;
        }

        // Pull exact metadata stored on Day-1
        $meta = $this->state->getExaminer($day1ExaminerId);

        return new SlotDTO(
            type: $slot->type,
            examinerId: $day1ExaminerId,
            name: $meta['name'],
            mobile: $meta['mobile'],
            status: $meta['status'] ?? 'Assigned',
            colour: $meta['colour'] ?? 'bg-success'
        );
    }
}
