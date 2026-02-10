<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\DTO\SlotDTO;

class DummyRule implements AllocationRule
{
    public function applies(array $context): bool
    {
        return true;
    }

    public function process(SlotDTO $slot, array $context): SlotDTO
    {
        if ($slot->examinerId) return $slot;

        return new SlotDTO(
            type: $slot->type,
            examinerId: 0,
            name: 'Assignment Pending dummy',
            status: 'Assignment Pending dummy',
            colour: '#6c757d'
        );
    }
}
