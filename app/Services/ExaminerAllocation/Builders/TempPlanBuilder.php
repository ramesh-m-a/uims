<?php

namespace App\Services\ExaminerAllocation\Builders;

use App\Repositories\TempAllocationRepository;
use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\ExaminerAllocation\AllocationResult;
use App\Services\ExaminerAllocation\DTO\AllocationRowDTO;
use Illuminate\Support\Collection;

class TempPlanBuilder
{
    public function __construct(
        protected TempAllocationRepository $tempRepo,
    ) {}

    public function hasDraft(AllocationContext $context): bool
    {
      //  dd('TempPlanBuilder - hasDraft');
        return $this->tempRepo->existsForContext($context);
    }

    public function build(AllocationContext $context): AllocationResult
    {
     //   dd('TempPlanBuilder - build');
        $rows = $this->tempRepo->fetchForContext($context);

        if ($rows->isEmpty()) {
            return AllocationResult::empty();
        }

        $collection = collect();
        $statuses = collect();

        foreach ($rows as $r) {

            $statusLabel = $r->status_label
                ?? match ((int)$r->status) {
                    27 => 'Assignment Pending Temp',
                    26 => 'Assigned Temp',
                    default => (string)$r->status,
                };

            $statuses->push($statusLabel);

            $collection->push(
                new AllocationRowDTO(
                    examinerId: (int) $r->examiner_id,
                    name: (string) $r->examiner_name,
                    mobile: (string) $r->mobile,
                    status: $statusLabel,
                    colour: (string) ($r->label_colour ?: '#6c757d'),

                    batchName: (string) $r->batch_name,
                    subjectName: (string) $r->subject_name,
                    centreName: (string) $r->centre_name,

                    fromDate: (string) $r->from_date,
                    toDate: (string) $r->to_date,

                    rangeId: (int) $r->batch_range_id,
                    batchId: (int) $r->batch_id,

                    yearId: (int) $r->year_id,
                    monthId: (int) $r->month_id,
                    schemeId: (int) $r->scheme_id,
                    degreeId: (int) $r->degree_id,

                    subjectId: (int) $r->subject_id,
                    centreId: (int) $r->centre_id,

                    examinerType: (string) $r->examiner_type,
                    examinerTypeId: (int) $r->examiner_type_id,
                    statusId: (int) $r->status_id,
                )
            );
        }

        return new AllocationResult(
            rows: $collection,
            statuses: $statuses->unique()->values()->all(),
            shortfalls: []
        );
    }
}
