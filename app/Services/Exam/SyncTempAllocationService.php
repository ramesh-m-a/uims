<?php

namespace App\Services\Exam;

use Illuminate\Support\Facades\DB;

class SyncTempAllocationService
{
    public static function syncBatch(int $batchId): void
    {
        DB::transaction(function () use ($batchId) {

            /*
            -------------------------------------------------
            STEP 1 — ACTIVE RANGES (REAL SOURCE OF TRUTH)
            -------------------------------------------------
            */
            $ranges = DB::table('mas_batch_range')
                ->where('mas_batch_range_batch_id', $batchId)
                ->where('mas_batch_range_status_id', 1)
                ->orderBy('mas_batch_range_from_date')
                ->get();

            if ($ranges->isEmpty()) {
                return;
            }

            $rangeMap = $ranges->keyBy(function ($r) use ($batchId) {
                return $batchId . '|' .
                    $r->mas_batch_range_centre_id . '|' .
                    $r->mas_batch_range_from_date;
            });

            $activeRangeIds = $ranges->pluck('id')->toArray();

            /*
            -------------------------------------------------
            STEP 2 — LOAD TEMP WITH RANGE JOIN
            -------------------------------------------------
            */
            $temps = DB::table('temp_examiner_assigned_details as t')
                ->leftJoin('mas_batch_range as r', 'r.id', '=', 't.batch_range_id')
                ->where('t.batch_id', $batchId)
                ->select(
                    't.*',
                    'r.mas_batch_range_from_date as range_date',
                    'r.mas_batch_range_centre_id as range_centre'
                )
                ->get();

            $tempMap = $temps
                ->filter(fn($t) => $t->range_date !== null)
                ->keyBy(function ($t) use ($batchId) {
                    return $batchId . '|' .
                        $t->centre_id . '|' .
                        $t->range_date;
                });

            /*
            -------------------------------------------------
            STEP 3 — SAFE REMAP (DATE BASED)
            -------------------------------------------------
            */
            foreach ($rangeMap as $key => $range) {

                if (!isset($tempMap[$key])) continue;

                $temp = $tempMap[$key];

                $dateChanged =
                    $temp->from_date !== $range->mas_batch_range_from_date ||
                    $temp->batch_range_id != $range->id;

                DB::table('temp_examiner_assigned_details')
                    ->where('id', $temp->id)
                    ->update([
                        'batch_range_id' => $range->id,
                        'from_date'      => $range->mas_batch_range_from_date,
                        'to_date'        => $range->mas_batch_range_to_date,
                        'is_rescheduled' => $dateChanged ? 1 : $temp->is_rescheduled,
                        'status'         => $temp->status,
                        'updated_at'     => now(),
                    ]);
            }

            /*
            -------------------------------------------------
            STEP 4 — ORPHAN → INACTIVE
            -------------------------------------------------
            */
            DB::table('temp_examiner_assigned_details')
                ->where('batch_id', $batchId)
                ->whereNotIn('batch_range_id', $activeRangeIds)
                ->update([
                    'status' => 2, // In Active
                    'is_rescheduled' => 1,
                    'updated_at' => now(),
                ]);

            /*
            -------------------------------------------------
            STEP 5 — SAFE TEMPLATE CLONE (NEW ROW STATUS = 26)
            -------------------------------------------------
            */
            foreach ($ranges as $range) {

                $exists = DB::table('temp_examiner_assigned_details')
                    ->where('batch_id', $batchId)
                    ->where('batch_range_id', $range->id)
                    ->exists();

                if ($exists) continue;

                $template = DB::table('temp_examiner_assigned_details as t')
                    ->join('mas_batch_range as r', 'r.id', '=', 't.batch_range_id')
                    ->where('t.batch_id', $batchId)
                    ->where('t.centre_id', $range->mas_batch_range_centre_id)
                    ->orderByDesc('r.mas_batch_range_from_date')
                    ->orderByDesc('t.id')
                    ->select('t.*')
                    ->first();

                if (!$template) continue;

                DB::table('temp_examiner_assigned_details')->insert([
                    'user_id'          => $template->user_id,
                    'year_id'          => $template->year_id,
                    'month_id'         => $template->month_id,
                    'scheme_id'        => $template->scheme_id,
                    'degree_id'        => $template->degree_id,
                    'batch_id'         => $batchId,
                    'batch_range_id'   => $range->id,
                    'examiner_id'      => $template->examiner_id,
                    'examiner_name'    => $template->examiner_name,
                    'examiner_type'    => $template->examiner_type,
                    'examiner_type_id' => $template->examiner_type_id,
                    'mobile'           => $template->mobile,
                    'centre_id'        => $range->mas_batch_range_centre_id,
                    'centre_name'      => $template->centre_name,
                    'subject_id'       => $template->subject_id,
                    'subject_name'     => $template->subject_name,

                    // ⭐ FINAL RULE — NEW ROWS ALWAYS START AS 16
                    'status'           => 16,
                    'status_label'     => $template->status_label,

                    'from_date'        => $range->mas_batch_range_from_date,
                    'to_date'          => $range->mas_batch_range_to_date,
                    'is_rescheduled'   => 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            /*
            -------------------------------------------------
            STEP 6 — GOLDEN RULE (LAST DATE INACTIVE)
            -------------------------------------------------
            */
            if ($ranges->count() > 1) {

                $lastRange = $ranges
                    ->sortByDesc('mas_batch_range_from_date')
                    ->first();

                DB::table('temp_examiner_assigned_details')
                    ->where('batch_id', $batchId)
                    ->where('batch_range_id', $lastRange->id)
                    ->update([
                        'status' => 2, // In Active
                        'is_rescheduled' => 1,
                        'updated_at' => now(),
                    ]);
            }

        });
    }
}
