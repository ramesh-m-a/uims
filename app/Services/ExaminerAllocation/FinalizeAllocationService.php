<?php

namespace App\Services\ExaminerAllocation;

use Illuminate\Support\Facades\DB;

class FinalizeAllocationService
{
    public function finalize(
        int $userId,
        int $yearId,
        int $monthId,
        int $schemeId,
        int $degreeId
    ): void {
        DB::transaction(function () use (
            $userId,
            $yearId,
            $monthId,
            $schemeId,
            $degreeId
        ) {

            // 1. Fetch all temp rows for this session
            $rows = DB::table('temp_examiner_assigned_details')
                ->where('user_id', $userId)
                ->where('year_id', $yearId)
                ->where('month_id', $monthId)
                ->where('scheme_id', $schemeId)
                ->where('degree_id', $degreeId)
                ->get();

            if ($rows->isEmpty()) {
                return;
            }

            $now = now();
            $insert = [];

            foreach ($rows as $r) {
                $insert[] = [
                    // Core identifiers
                    'examiner_assigned_details_examiner_id'       => (int) $r->examiner_id,

                    'examiner_assigned_details_year_id'           => (int) $r->year_id,
                    'examiner_assigned_details_month_id'          => (int) $r->month_id,

                    'examiner_assigned_details_batch_id'          => (int) $r->batch_id,
                    'examiner_assigned_details_batch_range_id'    => (int) $r->batch_range_id,

                    'examiner_assigned_details_status_id'         => (int) $r->status,

                    // Type
                    'examiner_assigned_details_type'              => (string) $r->examiner_type,

                    // Scope / ownership
                    'examiner_assigned_details_centre_id'         => (int) $r->centre_id,
                    'examiner_assigned_details_attached_id'       => (int) ($r->attached_id ?? 0),

                    'examiner_assigned_details_revised_scheme_id' => (int) $r->scheme_id,
                    'examiner_assigned_details_subject_id'        => (int) $r->subject_id,

                    'examiner_assigned_details_degree_id'         => (int) $r->degree_id,
                    'examiner_assigned_details_stream_id'         => (int) $r->user_stream_id,

                    // Dates
                    'examiner_assigned_details_from_date'         => $r->from_date,
                    'examiner_assigned_details_to_date'           => $r->to_date,

                    // Flags
                    'examiner_assigned_details_is_additional'     => 0,

                    // Audit
                    'created_at'                                  => $now,
                    'updated_at'                                  => $now,
                ];
            }

            // 2. Insert into final table
            foreach (array_chunk($insert, 1000) as $chunk) {
                DB::table('examiner_assigned_details')->insert($chunk);
            }

            // 3. Delete temp rows
            DB::table('temp_examiner_assigned_details')
                ->where('user_id', $userId)
                ->where('year_id', $yearId)
                ->where('month_id', $monthId)
                ->where('scheme_id', $schemeId)
                ->where('degree_id', $degreeId)
                ->delete();
        });
    }
}
