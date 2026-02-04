<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ExaminerAllocation\AllocationContext;

class AllocationRepository
{
    public function existsForContext(AllocationContext $context): bool
    {
        return DB::table('examiner_assigned_details')
            ->where('examiner_assigned_details_year_id', $context->yearId)
            ->where('examiner_assigned_details_month_id', $context->monthId)
            ->where('examiner_assigned_details_revised_scheme_id', $context->schemeId)
            ->where('examiner_assigned_details_degree_id', $context->degreeId)
            ->exists();
    }

    public function fetchForContext(AllocationContext $context): Collection
    {

        dd('fetchForContext');
       $base = DB::table('examiner_assigned_details as ead')
            ->leftJoin('mas_batch_range as br', 'br.id', '=', 'ead.examiner_assigned_details_batch_range_id')
            ->leftJoin('mas_batch as b', 'b.id', '=', 'ead.examiner_assigned_details_batch_id')
            ->leftJoin('mas_subject as s', 's.id', '=', 'ead.examiner_assigned_details_subject_id')
            ->leftJoin('mas_college as c', 'c.id', '=', 'ead.examiner_assigned_details_centre_id')
            ->leftJoin('examiner_details as ed', 'ed.id', '=', 'ead.examiner_assigned_details_examiner_id')
            ->leftJoin('basic_details as bd', 'bd.id', '=', 'ed.examiner_details_basic_details_id')
            ->leftJoin('users as u', 'u.id', '=', 'bd.basic_details_user_id')
            ->leftJoin('mas_status as ms', 'ms.id', '=', 'ead.examiner_assigned_details_status_id')
            ->where('ead.examiner_assigned_details_year_id', $context->yearId)
            ->where('ead.examiner_assigned_details_month_id', $context->monthId)
            ->where('ead.examiner_assigned_details_revised_scheme_id', $context->schemeId)
            ->where('ead.examiner_assigned_details_degree_id', $context->degreeId)
            ->select([
                'examiner_assigned_details_examiner_id',
                'examiner_assigned_details_year_id',
                'examiner_assigned_details_month_id',
                'examiner_assigned_details_batch_id',
                'examiner_assigned_details_batch_range_id',
                'examiner_assigned_details_status_id',
                'examiner_assigned_details_basic_details_id',
                'examiner_assigned_details_type',
                'examiner_assigned_details_centre_id',
                'examiner_assigned_details_attached_id',
                'examiner_assigned_details_revised_scheme_id',
                'examiner_assigned_details_subject_id',
                'examiner_assigned_details_from_date',
                'examiner_assigned_details_to_date',
                'examiner_assigned_details_degree_id',
                'examiner_assigned_details_stream_id',

                'b.mas_batch_name as batch_name',
                's.mas_subject_name as subject_name',
                'c.mas_college_name as centre_name',

                'br.mas_batch_range_from_date as from_date',
                'br.mas_batch_range_to_date as to_date',

                'ms.mas_status_name',
                'ms.mas_status_label_colour',

                DB::raw("CONCAT(COALESCE(u.fname,''),' ',COALESCE(u.mname,''),' ',COALESCE(u.lname,'')) as examiner_name"),
                'u.mobile as mobile',
            ])
            ->orderBy('ead.examiner_assigned_details_batch_id')
            ->orderBy('ead.examiner_assigned_details_batch_range_id')
            ->get();

        dd('BASE ROWS', $base);

        return DB::table('examiner_assigned_details as ead')
            ->leftJoin('mas_batch_range as br', 'br.id', '=', 'ead.examiner_assigned_details_batch_range_id')
            ->leftJoin('mas_batch as b', 'b.id', '=', 'ead.examiner_assigned_details_batch_id')
            ->leftJoin('mas_subject as s', 's.id', '=', 'ead.examiner_assigned_details_subject_id')
            ->leftJoin('mas_college as c', 'c.id', '=', 'ead.examiner_assigned_details_centre_id')
            ->leftJoin('examiner_details as ed', 'ed.id', '=', 'ead.examiner_assigned_details_examiner_id')
            ->leftJoin('basic_details as bd', 'bd.id', '=', 'ed.examiner_details_basic_details_id')
            ->leftJoin('users as u', 'u.id', '=', 'bd.basic_details_user_id')
            ->leftJoin('mas_status as ms', 'ms.id', '=', 'ead.examiner_assigned_details_status_id')
            ->where('ead.examiner_assigned_details_year_id', $context->yearId)
            ->where('ead.examiner_assigned_details_month_id', $context->monthId)
            ->where('ead.examiner_assigned_details_revised_scheme_id', $context->schemeId)
            ->where('ead.examiner_assigned_details_degree_id', $context->degreeId)
            ->select([
                'examiner_assigned_details_examiner_id',
                'examiner_assigned_details_year_id',
                'examiner_assigned_details_month_id',
                'examiner_assigned_details_batch_id',
                'examiner_assigned_details_batch_range_id',
                'examiner_assigned_details_status_id',
                'examiner_assigned_details_basic_details_id',
                'examiner_assigned_details_type',
                'examiner_assigned_details_centre_id',
                'examiner_assigned_details_attached_id',
                'examiner_assigned_details_revised_scheme_id',
                'examiner_assigned_details_subject_id',
                'examiner_assigned_details_from_date',
                'examiner_assigned_details_to_date',
                'examiner_assigned_details_degree_id',
                'examiner_assigned_details_stream_id',

                'b.mas_batch_name as batch_name',
                's.mas_subject_name as subject_name',
                'c.mas_college_name as centre_name',

                'br.mas_batch_range_from_date as from_date',
                'br.mas_batch_range_to_date as to_date',

                'ms.mas_status_name',
                'ms.mas_status_label_colour',

                DB::raw("CONCAT(COALESCE(u.fname,''),' ',COALESCE(u.mname,''),' ',COALESCE(u.lname,'')) as examiner_name"),
                'u.user_mobile_number as mobile',
            ])
            ->orderBy('ead.examiner_assigned_details_batch_id')
            ->orderBy('ead.examiner_assigned_details_batch_range_id')
            ->orderBy('c.mas_college_name')
            ->get();
    }

    public function groupedByRange(int $batchId): array
    {
        return DB::table('examiner_assigned_details')
            ->where('examiner_assigned_details_batch_id', $batchId)
            ->get()
            ->groupBy('examiner_assigned_details_batch_range_id')
            ->toArray();
    }

    public function markUnassignedForBatch(int $batchId, array $examinerIds): int
    {
        return DB::table('examiner_assigned_details')
            ->where('examiner_assigned_details_batch_id', $batchId)
            ->whereIn('examiner_assigned_details_examiner_id', $examinerIds)
            ->update(['updated_at' => now()]);
    }

    public function cloneRange(
        int $batchId,
        array $sourceRows,
        int $targetRangeId,
        string $fromDate,
        string $toDate,
        ?int $centreId,
        ?string $attachedId,
    ): void {
        $inserts = [];

        foreach ($sourceRows as $row) {
            $inserts[] = [
                'examiner_assigned_details_examiner_id' => $row->examiner_assigned_details_examiner_id,
                'examiner_assigned_details_year_id' => $row->examiner_assigned_details_year_id,
                'examiner_assigned_details_month_id' => $row->examiner_assigned_details_month_id,
                'examiner_assigned_details_batch_id' => $batchId,
                'examiner_assigned_details_batch_range_id' => $targetRangeId,
                'examiner_assigned_details_revised_scheme_id' => $row->examiner_assigned_details_revised_scheme_id,
                'examiner_assigned_details_subject_id' => $row->examiner_assigned_details_subject_id,
                'examiner_assigned_details_degree_id' => $row->examiner_assigned_details_degree_id,
                'examiner_assigned_details_stream_id' => $row->examiner_assigned_details_stream_id,
                'examiner_assigned_details_type' => $row->examiner_assigned_details_type,
                'examiner_assigned_details_status_id' => 44,
                'examiner_assigned_details_from_date' => $fromDate,
                'examiner_assigned_details_to_date' => $toDate,
                'examiner_assigned_details_centre_id' => $centreId,
                'examiner_assigned_details_attached_id' => $attachedId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('examiner_assigned_details')->insert($inserts);
    }

    public function findAssignmentRow(int $examinerId, int $batchId, int $rangeId): ?object
    {
        return DB::table('examiner_assigned_details')
            ->where('examiner_assigned_details_examiner_id', $examinerId)
            ->where('examiner_assigned_details_batch_id', $batchId)
            ->where('examiner_assigned_details_batch_range_id', $rangeId)
            ->first();
    }

    public function updateAssignmentDates(
        int $examinerId,
        int $batchId,
        int $rangeId,
        string $fromDate,
        string $toDate
    ): int {
        return DB::table('examiner_assigned_details')
            ->where('examiner_assigned_details_examiner_id', $examinerId)
            ->where('examiner_assigned_details_batch_id', $batchId)
            ->where('examiner_assigned_details_batch_range_id', $rangeId)
            ->update([
                'examiner_assigned_details_from_date' => $fromDate,
                'examiner_assigned_details_to_date'   => $toDate,
                'updated_at' => now(),
            ]);
    }


}
