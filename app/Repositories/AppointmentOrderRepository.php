<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Services\ExaminerAllocation\AllocationContext;

class AppointmentOrderRepository
{
    public function fetchForCollegeContext(AllocationContext $context)
    {
        /** ⭐ STEP 1 — GET BASE ROWS FROM EXISTING WORKING REPO */
        $baseRows = app(TempAllocationRepository::class)
            ->fetchForCollegeContext($context);

        if ($baseRows->isEmpty()) {
            return $baseRows;
        }

        /** ⭐ STEP 2 — EXTRACT TEMP ALLOCATION IDS */
        $allocationIds = collect($baseRows)
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        /** ⭐ STEP 3 — FETCH APPOINTMENT ORDERS ONLY FOR THOSE */
        $orders = DB::table('appointment_orders')
            ->whereIn('allocation_id', $allocationIds)
            ->select([
                'allocation_id',
                'order_number',
                'pdf_path'
            ])
            ->get()
            ->keyBy('allocation_id');

        /** ⭐ STEP 4 — MERGE INTO BASE ROW STRUCTURE */
        return collect($baseRows)
            ->map(function ($row) use ($orders) {

                $order = $orders[$row->id] ?? null;

                $row->order_number = $order->order_number ?? null;
                $row->pdf_path     = $order->pdf_path ?? null;

                return $row;
            })
            ->sortBy([
                ['centre_name', 'asc'],
                ['batch_name', 'asc'],
                ['from_date', 'asc'],
                ['examiner_type', 'asc'], // optional but good
            ])
            ->values();
    }
}
