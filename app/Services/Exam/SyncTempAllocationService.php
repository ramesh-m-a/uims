<?php

namespace App\Services\Exam;

use Illuminate\Support\Facades\DB;

class SyncTempAllocationService
{
    public static function syncBatch(int $batchId): void
    {
        DB::transaction(function () use ($batchId) {

            // ---------------------------------
            // 1. Get ACTIVE batch ranges
            // ---------------------------------
            $ranges = DB::table('mas_batch_range')
                ->where('mas_batch_range_batch_id', $batchId)
                ->where('mas_batch_range_status_id', 1)
                ->get()
                ->keyBy('id');

            if ($ranges->isEmpty()) {
                return;
            }

            // ---------------------------------
            // 2. Existing temp rows
            // ---------------------------------
            $tempRows = DB::table('temp_examiner_assigned_details')
                ->where('batch_id', $batchId)
                ->get();

            $tempByRange = $tempRows->groupBy('batch_range_id');

            // ---------------------------------
            // 3. Sync EACH ACTIVE RANGE
            // ---------------------------------
            foreach ($ranges as $rangeId => $range) {

                // Already exists → just sync dates
                if (isset($tempByRange[$rangeId])) {

                    DB::table('temp_examiner_assigned_details')
                        ->where('batch_range_id', $rangeId)
                        ->update([
                            'from_date' => $range->mas_batch_range_from_date,
                            'to_date'   => $range->mas_batch_range_to_date,
                            'updated_at'=> now(),
                        ]);

                    continue;
                }

                // ---------------------------------
                // NEW RANGE → clone nearest existing
                // ---------------------------------

                $closest = DB::table('temp_examiner_assigned_details')
                    ->where('batch_id', $batchId)
                    ->orderByRaw('ABS(DATEDIFF(from_date, ?))', [
                        $range->mas_batch_range_from_date
                    ])
                    ->get();

                if ($closest->isEmpty()) {
                    continue;
                }

                foreach ($closest as $row) {

                    DB::table('temp_examiner_assigned_details')->insert([
                        'user_id' => $row->user_id,
                        'year_id' => $row->year_id,
                        'month_id'=> $row->month_id,
                        'scheme_id'=> $row->scheme_id,
                        'degree_id'=> $row->degree_id,

                        'batch_id'=> $row->batch_id,
                        'batch_range_id'=> $rangeId,

                        'examiner_id'=> $row->examiner_id,
                        'examiner_name'=> $row->examiner_name,
                        'examiner_type'=> $row->examiner_type,
                        'examiner_type_id'=> $row->examiner_type_id,
                        'mobile'=> $row->mobile,

                        'centre_id'=> $row->centre_id,
                        'centre_name'=> $row->centre_name,

                        'subject_id'=> $row->subject_id,
                        'subject_name'=> $row->subject_name,

                        'status'=> $row->status,
                        'status_label'=> $row->status_label,

                        'batch_name'=> $range->mas_batch_range_batch_name,

                        'from_date'=> $range->mas_batch_range_from_date,
                        'to_date'=> $range->mas_batch_range_to_date,

                        'created_at'=> now(),
                        'updated_at'=> now(),
                    ]);
                }
            }

            // ---------------------------------
            // 4. Inactivate TEMP rows for DEAD ranges
            // ---------------------------------
            $activeRangeIds = $ranges->keys()->toArray();

            DB::table('temp_examiner_assigned_details')
                ->where('batch_id', $batchId)
                ->whereNotIn('batch_range_id', $activeRangeIds)
                ->update([
                    'status' => 2, // InActive
                    'updated_at' => now(),
                ]);
        });
    }
}
