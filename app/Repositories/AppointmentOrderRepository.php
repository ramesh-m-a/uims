<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Services\ExaminerAllocation\AllocationContext;

class AppointmentOrderRepository
{
    public function fetchForCollegeContext(AllocationContext $context)
    {
        /** ⭐ STEP 1 — GET BASE ROWS FROM EXISTING WORKING REPO */
        $baseRows = collect(
            app(TempAllocationRepository::class)
                ->fetchForCollegeContext($context)
        )->map(function ($r) {
            return is_array($r) ? (object) $r : $r;
        });

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
                'id',
                'allocation_id',
                'order_number',
                'pdf_path'
            ])
            ->get()
            ->keyBy('allocation_id');

        /** ⭐ STEP 4 — MERGE INTO BASE ROW STRUCTURE */
        return collect($baseRows)->map(function ($row) use ($orders) {

            $row = (object) $row;   // ⭐⭐⭐ CRITICAL LINE ⭐⭐⭐

            $order = $orders[$row->id] ?? null;

            $row->appointment_order_id = $order->id ?? null;   // ⭐ ADD THIS
            $row->order_number         = $order->order_number ?? null;
            $row->pdf_path             = $order->pdf_path ?? null;

            return $row;
        });
    }
}
