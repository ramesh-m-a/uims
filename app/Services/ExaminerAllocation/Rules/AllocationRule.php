<?php

namespace App\Services\ExaminerAllocation\Rules;

use App\Services\ExaminerAllocation\DTO\SlotDTO;

interface AllocationRule
{
    public function applies(array $context): bool;

    public function process(SlotDTO $slot, array $context): SlotDTO;
}
