<?php

namespace App\Services\ExaminerAllocation;

use App\Services\ExaminerAllocation\Builders\FreshPlanBuilder;
use App\Services\ExaminerAllocation\Builders\ExistingPlanBuilder;
use App\Services\ExaminerAllocation\Builders\TempPlanBuilder;

class AllocationEngine
{
    public function __construct(
        protected TempPlanBuilder $tempBuilder,
        protected ExistingPlanBuilder $existingBuilder,
        protected FreshPlanBuilder $freshBuilder,
    ) {}

    public function build(AllocationContext $context): AllocationResult
    {
      //  dd('AllocationEngine');
        // Priority 1: user has temp draft
        if ($this->tempBuilder->hasDraft($context)) {
            return $this->tempBuilder->build($context);
        }

        // Priority 2: existing saved allocation
        if ($this->existingBuilder->hasExisting($context)) {
            return $this->existingBuilder->build($context);
        }

        // Priority 3: first time → generate fresh plan
        return $this->freshBuilder->build($context);
    }
}
