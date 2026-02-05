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
            STEP 1 — ACTIVE RANGES (SOURCE OF TRUTH)
            -------------------------------------------------
            */
            $ranges = DB::table('mas_batch_range')
                ->where('mas_batch_range_batch_id', $batchId)
                ->where('mas_batch_range_status_id', 1)
                ->get();

            if ($ranges->isEmpty()) {
                return;
            }

            $rangeMap = $ranges->keyBy(function ($r) {
                return $r->mas_batch_range_centre_id . '|' . $r->mas_batch_range_group_number;
            });

            $rangeIdSet = $ranges->pluck('id')->toArray();

            /*
            -------------------------------------------------
            STEP 2 — TEMP ROWS + RANGE JOIN (FOR LOGICAL IDENTITY)
            -------------------------------------------------
            */
            $temps = DB::table('temp_examiner_assigned_details as t')
                ->leftJoin('mas_batch_range as r', 'r.id', '=', 't.batch_range_id')
                ->where('t.batch_id', $batchId)
                ->select(
                    't.*',
                    'r.mas_batch_range_group_number as group_number',
                    'r.mas_batch_range_centre_id as range_centre_id',
                    'r.mas_batch_range_status_id as range_status'
                )
                ->get();

            $tempMap = $temps
                ->filter(fn($t) => $t->group_number !== null)
                ->keyBy(fn($t) => $t->centre_id . '|' . $t->group_number);

            /*
            -------------------------------------------------
            STEP 3 — SAFE REMAP (ONLY IF RANGE STILL EXISTS)
            -------------------------------------------------
            */
            foreach ($rangeMap as $logicalKey => $range) {

                if (!isset($tempMap[$logicalKey])) {
                    continue;
                }

                $temp = $tempMap[$logicalKey];

                /*
                -------------------------------------------------
                CRITICAL RULE 1 — RANGE ID IS SACRED
                If original temp range no longer exists → DO NOT REMAP
                -------------------------------------------------
                */
                if (!in_array($temp->batch_range_id, $rangeIdSet)) {
                    continue;
                }

                /*
                -------------------------------------------------
                CRITICAL RULE 2 — CENTRE LINEAGE MUST MATCH
                -------------------------------------------------
                */
                if ($temp->range_centre_id != $range->mas_batch_range_centre_id) {
                    continue;
                }

                $dateChanged =
                    $temp->from_date !== $range->mas_batch_range_from_date ||
                    $temp->to_date !== $range->mas_batch_range_to_date ||
                    $temp->batch_range_id != $range->id;

                DB::table('temp_examiner_assigned_details')
                    ->where('id', $temp->id)
                    ->update([
                        'batch_range_id' => $range->id,
                        'from_date'      => $range->mas_batch_range_from_date,
                        'to_date'        => $range->mas_batch_range_to_date,

                        // Auto heal reschedule flag
                        'is_rescheduled' => $dateChanged ? 1 : 0,

                        // Preserve workflow
                        'status'         => $temp->status,

                        'updated_at'     => now(),
                    ]);
            }

            /*
            -------------------------------------------------
            STEP 4 — ORPHAN TEMP → HARD INACTIVE
            -------------------------------------------------
            */
            DB::table('temp_examiner_assigned_details')
                ->where('batch_id', $batchId)
                ->whereNotIn('batch_range_id', $rangeIdSet)
                ->update([
                    'status' => 2,
                    'is_rescheduled' => 1,
                    'updated_at' => now(),
                ]);

            /*
            -------------------------------------------------
            STEP 5 — SAFE TEMPLATE INSERT (CLEAN ONLY)
            -------------------------------------------------
            */
            foreach ($rangeMap as $logicalKey => $range) {

                $exists = DB::table('temp_examiner_assigned_details')
                    ->where('batch_range_id', $range->id)
                    ->exists();

                if ($exists) continue;

                /*
                SAFE TEMPLATE PICK
                */
                $template = DB::table('temp_examiner_assigned_details as t')
                    ->join('mas_batch_range as r', 'r.id', '=', 't.batch_range_id')
                    ->where('t.batch_id', $batchId)
                    ->where('t.centre_id', $range->mas_batch_range_centre_id)
                    ->where('r.mas_batch_range_group_number', $range->mas_batch_range_group_number)

                    // HARD SAFETY FILTERS
                    ->where('r.mas_batch_range_status_id', 1)
                    ->where('t.is_rescheduled', 0)
                    ->whereIn('t.status', [1, 26])

                    ->orderByDesc('t.id')
                    ->select('t.*')
                    ->first();

                if (!$template) continue;

                DB::table('temp_examiner_assigned_details')->insert([
                    'user_id'         => $template->user_id,
                    'year_id'         => $template->year_id,
                    'month_id'        => $template->month_id,
                    'scheme_id'       => $template->scheme_id,
                    'degree_id'       => $template->degree_id,

                    'batch_id'        => $batchId,
                    'batch_range_id'  => $range->id,

                    'examiner_id'     => $template->examiner_id,
                    'examiner_name'   => $template->examiner_name,
                    'examiner_type'   => $template->examiner_type,
                    'examiner_type_id'=> $template->examiner_type_id,
                    'mobile'          => $template->mobile,

                    'centre_id'       => $range->mas_batch_range_centre_id,
                    'centre_name'     => $template->centre_name,

                    'subject_id'      => $template->subject_id,
                    'subject_name'    => $template->subject_name,

                    'status'          => $template->status,
                    'status_label'    => $template->status_label,

                    'from_date'       => $range->mas_batch_range_from_date,
                    'to_date'         => $range->mas_batch_range_to_date,

                    'is_rescheduled'  => 1,

                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            /*
            -------------------------------------------------
            STEP 6 — GOLDEN RULE (ONLY IF MULTI RANGE ACTIVE)
            -------------------------------------------------
            */
            if ($ranges->count() > 1) {

                $lastRange = $ranges
                    ->sortBy('mas_batch_range_group_number')
                    ->last();

                DB::table('temp_examiner_assigned_details')
                    ->where('batch_id', $batchId)
                    ->where('batch_range_id', $lastRange->id)
                    ->update([
                        'status' => 2,
                        'is_rescheduled' => 1,
                        'updated_at' => now(),
                    ]);
            }

        });
    }
}
