<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\DTO\SlotDTO;

class CollisionRule implements AllocationRule
{
    public function applies(array $context): bool
    {
        return true;
    }

    public function process(SlotDTO $slot, array $context): SlotDTO
    {
        if (!$slot->examinerId) return $slot;

        $used = $context['globalUsed'][$context['date']] ?? [];

        if (in_array($slot->examinerId, $used)) {
            // reject examiner
            return new SlotDTO(type: $slot->type);
        }

        return $slot;
    }
}
