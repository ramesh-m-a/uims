<?php

namespace App\Services\ExaminerAllocation\Builders;

use App\Repositories\AllocationRepository;
use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\ExaminerAllocation\AllocationResult;
use App\Services\ExaminerAllocation\DTO\AllocationRowDTO;
use App\Services\ExaminerAllocation\Mutation\AssignmentMutationService;

class ExistingPlanBuilder
{
    public function __construct(
        protected AllocationRepository $allocationRepo,
        protected AssignmentMutationService $mutationService,
    ) {}

    public function hasExisting(AllocationContext $context): bool
    {
        return $this->allocationRepo->existsForContext($context);
    }

    public function build(AllocationContext $context): AllocationResult
    {
        // 🔥 mutation runs before fetch
        $this->mutationService->syncUpdatedBatches(
            $context->yearId,
            $context->monthId,
            $context->schemeId,
            $context->degreeId,
            $context->streamId,
        );

        $rows = $this->allocationRepo->fetchForContext($context);

        if ($rows->isEmpty()) {
            return AllocationResult::empty();
        }

        $collection = collect();
        $statuses = collect();

        foreach ($rows as $r) {

            $status = $r->mas_status_name ?? 'Unknown';
            $statuses->push($status);

            $collection->push(
                new AllocationRowDTO(
                    examinerId: (int) $r->examiner_assigned_details_examiner_id,
                    name: (string) $r->examiner_name,
                    mobile: (string) ($r->mobile ?? ''),
                    status: (string) $status,
                    colour: (string) ($r->mas_status_label_colour ?? '#6c757d'),

                    batchName: (string) $r->batch_name,
                    subjectName: (string) $r->subject_name,
                    centreName: (string) $r->centre_name,

                    fromDate: (string) $r->from_date,
                    toDate: (string) $r->to_date,

                    rangeId: (int) $r->examiner_assigned_details_batch_range_id,
                    batchId: (int) $r->examiner_assigned_details_batch_id,

                    yearId: (int) $r->examiner_assigned_details_year_id,
                    monthId: (int) $r->examiner_assigned_details_month_id,
                    schemeId: (int) $r->examiner_assigned_details_revised_scheme_id,
                    degreeId: (int) $r->examiner_assigned_details_degree_id,

                    subjectId: (int) $r->examiner_assigned_details_subject_id,
                    centreId: (int) $r->examiner_assigned_details_centre_id,

                    examinerType: (string) $r->examiner_assigned_details_type,
                    examinerTypeId: (int) match ($r->examiner_assigned_details_type) {
                        'Internal-C', 'Internal-A' => 1,
                        'External', 'External-O' => 2,
                        default => 0,
                    },

                    statusId: (int) $r->examiner_assigned_details_status_id,
                )
            );
        }

        return new AllocationResult(
            rows: $collection,
            statuses: $statuses->unique()->values()->all(),
            shortfalls: [] // shortfall logic comes later (rule layer)
        );
    }
}
