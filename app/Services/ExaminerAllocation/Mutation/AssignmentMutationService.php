<?php

namespace App\Services\ExaminerAllocation\Mutation;

use App\Repositories\BatchRepository;
use App\Repositories\AllocationRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentMutationService
{
    public function __construct(
        protected BatchRepository $batchRepo,
        protected AllocationRepository $allocationRepo,
    ) {}

    public function syncUpdatedBatches(
        int $yearId,
        int $monthId,
        int $schemeId,
        int $degreeId,
        int $streamId
    ): void {
        $batches = $this->batchRepo->updatedBatches(
            $yearId, $monthId, $schemeId, $degreeId, $streamId
        );

        foreach ($batches as $batch) {
            DB::transaction(function () use ($batch) {
                $this->replaceOldCentreExaminers($batch->id);
                $this->ensureRangesHaveAssignments($batch->id);

                $this->batchRepo->clearUpdatedFlag($batch->id);
            });
        }
    }

    protected function replaceOldCentreExaminers(int $batchId): void
    {
        $oldCentreIds = $this->batchRepo->oldCentreIds($batchId);

        if (empty($oldCentreIds)) return;

        $examinerIds = $this->batchRepo->examinerIdsFromCentres($oldCentreIds);

        if (empty($examinerIds)) return;

        $affected = $this->allocationRepo
            ->markUnassignedForBatch($batchId, $examinerIds);

        if ($affected > 0) {
            Log::info("Unassigned $affected examiners for batch {$batchId}");
        }
    }

    protected function ensureRangesHaveAssignments(int $batchId): void
    {
        $ranges = $this->batchRepo->ranges($batchId);
        $eadByRange = $this->allocationRepo->groupedByRange($batchId);

        // pick first non-empty range as template
        $templateRange = collect($eadByRange)
            ->first(fn($rows) => !empty($rows));

        foreach ($ranges as $range) {
            if (!isset($eadByRange[$range->id])) {
                if ($templateRange) {
                    $this->allocationRepo->cloneRange(
                        $batchId,
                        sourceRows: $templateRange,
                        targetRangeId: $range->id,
                        fromDate: $range->from_date,
                        toDate: $range->to_date,
                        centreId: $range->centre_id,
                        attachedId: $range->attached_id,
                    );
                } else {
                    $this->allocationRepo->createDefaultSlots(
                        batchId: $batchId,
                        range: $range
                    );
                }
            }
        }
    }
}
