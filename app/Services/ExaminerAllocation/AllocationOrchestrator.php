<?php

namespace App\Services\ExaminerAllocation;

use App\Services\ExaminerAllocation\Builders\FreshPlanBuilder;
use Illuminate\Support\Collection;

class AllocationOrchestrator
{
    public function __construct(
        protected FreshPlanBuilder $builder,
        protected TempAllocationPersister $persister
    ) {}

    /**
     * Full legacy-equivalent pipeline:
     * Build → Normalize slots → Persist temp → return rows
     */
    public function run(AllocationContext $context): AllocationResult
    {
        // 1. Pure allocation (your current engine)
        $result = $this->builder->build($context);

        // 2. Legacy behavior: ensure IC/IA/E/EO exist per slot
        $rows = $this->ensureFourTypesForDisplay(collect($result->rows));

        // 3. Persist exactly like legacy
        $this->persister->persist(
            userId: $context->userId,
            yearId: $context->yearId,
            monthId: $context->monthId,
            schemeId: $context->schemeId,
            degreeId: $context->degreeId,
            rows: $rows
        );

        // 4. Return corrected result (UI/debug parity)
        return new AllocationResult(
            rows: $rows,
            statuses: collect($rows)->pluck('status')->unique()->values()->all(),
            shortfalls: $result->shortfalls
        );
    }

    /* ------------------------------------------------------------
     |  Direct copy of your legacy logic (minimally adapted)
     |------------------------------------------------------------ */

    private function ensureFourTypesForDisplay(Collection $rows): Collection
    {
        $out = collect();

        $rows = $rows->unique(function ($r) {
            return implode('|', [
                (string)($r->centreName ?? ''),
                (string)($r->subjectName ?? ''),
                (string)($r->fromDate ?? ''),
                (string)($r->examinerType ?? ''),
            ]);
        })->values();

        $grouped = $rows->groupBy(function ($r) {
            return implode('|', [
                (int)$r->yearId,
                (int)$r->monthId,
                (int)$r->schemeId,
                (int)$r->batchId,
                (int)$r->rangeId,
                (int)$r->centreId,
                (int)$r->subjectId,
                (string)$r->fromDate,
            ]);
        });

        foreach ($grouped as $items) {
            foreach ($items as $it) $out->push($it);

            $types = $items->pluck('examinerType')->values();
            $proto = $items->first();

            $schemeId = (int)$proto->schemeId;
            $deptId   = (int)($proto->departmentId ?? 0);

            $isOrtho = ($deptId === 504);
            $isScheme = in_array($schemeId, [1,2,3,4], true);

            $add = function ($type) use (&$types, $proto, $out) {
                if (!$types->contains($type)) {
                    $out->push($this->makeDummy($type, $proto));
                    $types->push($type);
                }
            };

            if ($isOrtho) {
                $add('Internal-C');
                $add('External');
                continue;
            }

            $add('Internal-C');
            $add('Internal-A');
            $add('External');
            if ($isScheme) $add('External-O');
        }

        return $out->values();
    }

    private function makeDummy(string $type, $proto)
    {
        static $ctr = ['IC'=>0,'IA'=>0,'EX'=>0,'EO'=>0];

        switch ($type) {
            case 'Internal-C': $ctr['IC']++; $id = -1100 - $ctr['IC']; $tid = 1; break;
            case 'Internal-A': $ctr['IA']++; $id = -1200 - $ctr['IA']; $tid = 1; break;
            case 'External':   $ctr['EX']++; $id = -1300 - $ctr['EX']; $tid = 2; break;
            case 'External-O': $ctr['EO']++; $id = -1400 - $ctr['EO']; $tid = 2; break;
            default: $id = -1999; $tid = 0;
        }

        return (object)[
            'examinerId'     => $id,
            'name'           => "Shortfall - ($type)",
            'mobile'         => '',
            'status'         => 'Assignment Pending ramesh dummy',
            'colour'         => '#6c757d',
            'examinerType'   => $type,
            'examinerTypeId' => $tid,

            'batchName'   => $proto->batchName,
            'subjectName' => $proto->subjectName,
            'centreName'  => $proto->centreName,
            'fromDate'    => $proto->fromDate,
            'toDate'      => $proto->toDate,

            'rangeId'     => $proto->rangeId,
            'batchId'     => $proto->batchId,
            'yearId'      => $proto->yearId,
            'monthId'     => $proto->monthId,
            'schemeId'    => $proto->schemeId,
            'degreeId'    => $proto->degreeId,
            'subjectId'   => $proto->subjectId,
            'centreId'    => $proto->centreId,
        ];
    }
}
