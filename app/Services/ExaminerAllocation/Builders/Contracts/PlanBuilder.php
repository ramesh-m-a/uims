<?php

namespace App\Services\ExaminerAllocation\Builders\Contracts;

use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\ExaminerAllocation\AllocationResult;

interface PlanBuilder
{
    public function hasDraft(AllocationContext $context): bool;

    public function hasExisting(AllocationContext $context): bool;

    public function build(AllocationContext $context): AllocationResult;
}
