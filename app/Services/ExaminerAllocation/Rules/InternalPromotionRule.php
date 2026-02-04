<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\DTO\SlotDTO;
use App\Services\ExaminerAllocation\Domain\SlotType;
use App\Services\ExaminerAllocation\Pickers\ExaminerPicker;

class InternalPromotionRule implements AllocationRule
{
    public function __construct(
        protected ExaminerPicker $picker
    ) {}

    public function applies(array $context): bool
    {
        return true;
    }

    public function process(SlotDTO $slot, array $context): SlotDTO
    {
        $range = $context['range'];

        // Only applies to Internal-A slot
        if ($slot->type !== SlotType::INTERNAL_A) {
            return $slot;
        }

        // If IA already filled, nothing to do
        if ($slot->examinerId) {
            return $slot;
        }

        // ----------------------------
        // Step 1: Check if REAL IA exists
        // ----------------------------
        $realIaCandidate = $this->picker->pick(
            slotType: SlotType::INTERNAL_A,
            departmentId: $range->department_id,
            centreId: $range->centre_id,
            attachedCentreIds: $range->attached_ids,
            excludeExaminerIds: $context['usedIds'] ?? [],
            date: $range->from_date
        );

        // If no real IA candidate → DO NOT promote IC
        if (!$realIaCandidate) {
            return $slot;
        }

        // ----------------------------
        // Step 2: Try promote IC → IA
        // ----------------------------
        $icSlot = $context['slots'][SlotType::INTERNAL_C] ?? null;

        if (!$icSlot || !$icSlot->examinerId) {
            return $slot;
        }

        // Promote IC into IA
        return new SlotDTO(
            type: SlotType::INTERNAL_A,
            examinerId: $icSlot->examinerId,
            name: $icSlot->name,
            mobile: $icSlot->mobile,
            status: $icSlot->status,
            colour: $icSlot->colour,
        );
    }
}
