<?php

namespace App\Http\Controllers;

use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\ExaminerAllocation\AllocationOrchestrator;

class ExaminerAllocationTestController extends Controller
{
    public function testold(AllocationOrchestrator $orchestrator)
    {
        // 👇 REAL filter centre (change this to any centreId you want to inspect)
      //  $debugCentreId = 365; // DR B R Ambedkar example

        $context = new AllocationContext(
            yearId: 3,
            monthId: 2,
            schemeId: 3,
            degreeId: 1,
            userId: auth()->id() ?? 1,
            streamId: 6
        );

        $result = $orchestrator->run($context);

        // 🔍 Filter only one centre for apple-to-apple debugging
      $filtered = collect($result->rows)
            ->filter(fn($r) => (int)$r->centreId === (int)$debugCentreId)
            ->values();

        $rows = $filtered->map(function ($r) {
            return [
                'centre'  => $r->centreName,
                'batch'   => $r->batchName,
                'date'    => $r->fromDate,
                'subject' => $r->subjectName,
                'type'    => $r->examinerType,
                'name'    => $r->name,
            ];
        });

      //  dd($rows);

       /* dd([
            'centreId'    => $debugCentreId,
            'rows_count'  => $filtered->count(),
            'shortfalls'  => collect($result->shortfalls)->values(),
            'statuses'    => collect($filtered)->pluck('status')->unique()->values(),
            'sample'      => $filtered->take(20),
        ]);*/
    }

    public function finalizeTest(
        \App\Services\ExaminerAllocation\FinalizeAllocationService $service
    ) {
        $service->finalize(
            userId: auth()->id() ?? 1,
            yearId: 3,
            monthId: 2,
            schemeId: 3,
            degreeId: 1
        );

        dd('Finalized. Rows moved from temp_examiner_assigned_details to examiner_assigned_details');
    }

    public function test2(AllocationOrchestrator $orchestrator)
    {
        // Allow dynamic filters from URL like:
        // /allocation-test?year=3&month=2&scheme=3&degree=1&stream=6&centre=365

        $context = new AllocationContext(
            yearId:   (int) request('year', 3),
            monthId:  (int) request('month', 2),
            schemeId: (int) request('scheme', 3),
            degreeId: (int) request('degree', 1),
            userId:   auth()->id() ?? 1,
            streamId: (int) request('stream', 6),
        );

        $result = $orchestrator->run($context);

        $rows = collect($result->rows);

        // Optional centre filter (only if provided)
        if ($centreId = request('centre')) {
            $rows = $rows->filter(fn ($r) => (int) $r->centreId === (int) $centreId)
                ->values();
        }

        // Shape clean debug output
        $output = $rows->map(fn ($r) => [
            'centre'  => $r->centreName,
            'batch'   => $r->batchName,
            'date'    => $r->fromDate,
            'subject' => $r->subjectName,
            'type'    => $r->examinerType,
            'name'    => $r->name,
            'mobile'    => $r->mobile,
            'status'  => $r->status,
        ]);

        dd([
            'context'     => $context,
            'rows_count'  => $rows->count(),
            'shortfalls'  => collect($result->shortfalls)->values(),
            'statuses'    => $rows->pluck('status')->unique()->values(),
            'sample'      => $output->take(50),
        ]);
    }

    public function test(AllocationOrchestrator $orchestrator)
    {
        $context = new AllocationContext(
            yearId:   (int) request('year', 3),
            monthId:  (int) request('month', 2),
            schemeId: (int) request('scheme', 3),
            degreeId: (int) request('degree', 1),
            userId:   auth()->id() ?? 1,
            streamId: (int) request('stream', 6),
        );

        $result = $orchestrator->run($context);

        $rows = collect($result->rows);

        // Optional centre filter
        if ($centreId = request('centre')) {
            $rows = $rows->filter(fn ($r) => (int)$r->centreId === (int)$centreId)
                ->values();
        }

      /*  dd([
            'context'     => $context,
            'rows_count'  => $rows->count(),
            'shortfalls'  => collect($result->shortfalls)->values(),
            'statuses'    => $rows->pluck('status')->unique()->values(),
            'sample'      => $output->take(50),
        ]);*/

        return view('debug.allocation', [
            'context' => $context,
            'rows'    => $rows,
        ]);
    }


}
