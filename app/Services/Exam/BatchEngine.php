<?php

namespace App\Services\Exam;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Master\Config\Exam\Batch;

class BatchEngine
{
    public static function regenerate(Batch $batch): array
    {
        // ---------------------------------
        // 1. Validation
        // ---------------------------------
        if (!$batch->mas_batch_start_date) {
            throw new \Exception("Batch {$batch->id} has no start date");
        }

        $rule = DB::table('mas_student_per_batch')->where('mas_student_per_batch_subject_id', $batch->mas_batch_subject_id)->where('mas_student_per_batch_degree_id', $batch->mas_batch_degree_id)->first();

        if (!$rule) {
            throw new \Exception("Batch rule not found");
        }

        $maxPerBatch = (int)$rule->mas_student_per_batch_total_number;
        $perDay = (int)$rule->mas_student_per_batch_per_day;

        // ---------------------------------
        // 2. Aggregate TOTAL students
        //    (only ACTIVE attached colleges)
        // ---------------------------------
        $totalStudents = DB::table('mas_batch_centre_student as bcs')->join('mas_batch_centre as bc', function ($join) {
            $join->on('bc.mas_batch_id', '=', 'bcs.mas_batch_id')->on('bc.mas_centre_id', '=', 'bcs.mas_centre_id');
        })->where('bcs.mas_batch_id', $batch->id)->where('bc.status_id', 1)->sum('bcs.student_count');

        if ($totalStudents <= 0) {
            DB::table('mas_batch')->where('id', $batch->id)->update(['mas_batch_total_students' => 0, 'mas_batch_total_batches' => 0, 'updated_at' => now(),]);

            return ['total_students' => 0, 'groups' => 0, 'days' => 0,];
        }

        // ---------------------------------
        // 3. Capture EXISTING ACTIVE ranges
        //    (for safe inactivation later)
        // ---------------------------------
        $existingRangeKeys = DB::table('mas_batch_range')->where('mas_batch_range_batch_id', $batch->id)->where('mas_batch_range_status_id', 1)->get()->map(function ($r) {
            return $r->mas_batch_range_from_date . '|' . $r->mas_batch_range_centre_id;
        })->toArray();

        $newRangeKeys = [];

        // ---------------------------------
        // 4. Master data (naming only)
        // ---------------------------------
        $subject = DB::table('mas_subject')->where('id', $batch->mas_batch_subject_id)->value('mas_subject_name');

        $year = DB::table('mas_year')->where('id', $batch->mas_batch_year_id)->value('mas_year_year');

        $month = DB::table('mas_month')->where('id', $batch->mas_batch_month_id)->value('mas_month_name');

        $scheme = DB::table('mas_revised_scheme')->where('id', $batch->mas_batch_revised_scheme_id)->value('mas_revised_scheme_short_name');

        $degreeMap = [1 => 'UG', 2 => 'PG', 3 => 'PG-SS'];
        $degree = $degreeMap[$batch->mas_batch_degree_id] ?? 'UNK';

        // ---------------------------------
        // 5. Plan batches & days
        // ---------------------------------
        $groups = (int)ceil($totalStudents / $maxPerBatch);
        $startDate = Carbon::parse($batch->mas_batch_start_date);
        $day = 1;
        $totalDays = 0;

        for ($g = 1; $g <= $groups; $g++) {

            $groupStudents = ($g === $groups) ? $totalStudents - ($maxPerBatch * ($groups - 1)) : $maxPerBatch;

            // NOTE: Split remains exam-centre level only
            $splitId = DB::table('mas_batch_split')->insertGetId(['mas_batch_split_batch_id' => $batch->id, 'mas_batch_split_centre_id' => $batch->mas_batch_centre_id, 'mas_batch_split_students' => $groupStudents, 'mas_batch_split_status_id' => 1, 'created_at' => now(), 'updated_at' => now(),]);

            $days = (int)ceil($groupStudents / $perDay);

            for ($d = 1; $d <= $days; $d++) {

                $examDate = $startDate->copy()->addDays($day - 1);

                $studentsToday = ($d === $days) ? $groupStudents - ($perDay * ($days - 1)) : $perDay;

                $batchName = strtoupper("{$scheme} - {$degree} - {$year} - {$month} - {$subject} - DAY {$day}");

                $rangeKey = $examDate->format('Y-m-d') . '|' . $batch->mas_batch_centre_id;
                $newRangeKeys[] = $rangeKey;

                // Reactivate if already exists, else insert
                $existingId = DB::table('mas_batch_range')->where('mas_batch_range_batch_id', $batch->id)->where('mas_batch_range_from_date', $examDate->format('Y-m-d'))->where('mas_batch_range_centre_id', $batch->mas_batch_centre_id)->value('id');

                if ($existingId) {
                    DB::table('mas_batch_range')->where('id', $existingId)->update(['mas_batch_range_students' => $studentsToday, 'mas_batch_range_batch_name' => $batchName, 'mas_batch_range_group_number' => $g, 'mas_batch_range_status_id' => 1, 'updated_at' => now(),]);
                } else {
                    DB::table('mas_batch_range')->insert(['mas_batch_range_batch_id' => $batch->id, 'mas_batch_range_batch_split_id' => $splitId, 'mas_batch_range_centre_id' => $batch->mas_batch_centre_id, 'mas_batch_range_from_date' => $examDate->format('Y-m-d'), 'mas_batch_range_to_date' => $examDate->format('Y-m-d'), 'mas_batch_range_students' => $studentsToday, 'mas_batch_range_batch_name' => $batchName, 'mas_batch_range_group_number' => $g, 'mas_batch_range_status_id' => 1, 'created_at' => now(), 'updated_at' => now(),]);
                }

                $day++;
                $totalDays++;
            }
        }

        // ---------------------------------
        // 6. Inactivate ONLY obsolete ranges
        // ---------------------------------
        $obsoleteKeys = array_diff($existingRangeKeys, $newRangeKeys);

        foreach ($obsoleteKeys as $key) {
            [$date, $centreId] = explode('|', $key);

            DB::table('mas_batch_range')->where('mas_batch_range_batch_id', $batch->id)->where('mas_batch_range_from_date', $date)->where('mas_batch_range_centre_id', $centreId)->update(['mas_batch_range_status_id' => 2, 'updated_at' => now(),]);
        }

        // ---------------------------------
        // 7. Update batch summary
        // ---------------------------------
        DB::table('mas_batch')->where('id', $batch->id)->update(['mas_batch_total_students' => $totalStudents, 'mas_batch_total_batches' => $groups, 'updated_at' => now(),]);

        // ---------------------------------
// 8. Sync TEMP allocation
// ---------------------------------
        \App\Services\Exam\SyncTempAllocationService::syncBatch($batch->id);

        return ['total_students' => $totalStudents, 'groups' => $groups, 'days' => $totalDays,];
    }

}
