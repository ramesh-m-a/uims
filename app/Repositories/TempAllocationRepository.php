<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ExaminerAllocation\AllocationContext;
use App\Services\ExaminerAllocation\DTO\AllocationRowDTO;

class TempAllocationRepository
{
    public function fetchForContext(AllocationContext $context): Collection
    {
        return DB::table('temp_examiner_assigned_details')
            ->where('user_id', $context->userId)
            ->where('year_id', $context->yearId)
            ->where('month_id', $context->monthId)
            ->where('scheme_id', $context->schemeId)
            ->where('degree_id', $context->degreeId)
            ->whereIn('status', [49, 26])
            ->orderBy('batch_id')
            ->orderBy('batch_range_id')
            ->orderBy('id')
            ->get();
    }

    public function existsForContext(AllocationContext $context): bool
    {
        return DB::table('temp_examiner_assigned_details')
            ->where('user_id', $context->userId)
            ->where('year_id', $context->yearId)
            ->where('month_id', $context->monthId)
            ->where('scheme_id', $context->schemeId)
            ->where('degree_id', $context->degreeId)
            ->exists();
    }

    /**
     * Store generated engine rows into temp table
     */
    public function store(AllocationContext $context, iterable $rows): void
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                /** @var AllocationRowDTO $row */

                DB::table('temp_examiner_assigned_details')->insert([
                    'user_id'      => $context->userId,

                    'year_id'      => $row->yearId,
                    'month_id'     => $row->monthId,
                    'scheme_id'    => $row->schemeId,
                    'degree_id'    => $row->degreeId,

                    'batch_id'     => $row->batchId,
                    'batch_range_id' => $row->rangeId,

                    'subject_id'   => $row->subjectId,
                    'centre_id'    => $row->centreId,

                    'examiner_type'    => $row->examinerType,
                    'examiner_type_id' => $row->examinerTypeId,

                    'examiner_id'   => $row->examinerId,
                    'examiner_name' => $row->name,
                    'mobile'        => $row->mobile,

                    'batch_name'    => $row->batchName,
                    'subject_name'  => $row->subjectName,
                    'centre_name'   => $row->centreName,

                    'from_date'     => $row->fromDate,
                    'to_date'       => $row->toDate,

                    'status'        => $row->statusId,
                    'status_label'  => $row->status,
                    'label_colour'  => $row->colour,

                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function syncDatesFromBatchRange(AllocationContext $context): void
    {
        $ranges = DB::table('mas_batch_range')
            ->join('mas_batch as b', 'b.id', '=', 'mas_batch_range.mas_batch_range_batch_id')
            ->where('b.mas_batch_year_id', $context->yearId)
            ->where('b.mas_batch_month_id', $context->monthId)
            ->where('b.mas_batch_revised_scheme_id', $context->schemeId)
            ->where('b.mas_batch_degree_id', $context->degreeId)
            ->where('b.mas_batch_stream_id', $context->streamId)
            ->select([
                'mas_batch_range.id as range_id',
                'mas_batch_range.mas_batch_range_from_date as from_date',
                'mas_batch_range.mas_batch_range_to_date as to_date',
            ])
            ->get()
            ->keyBy('range_id');

        foreach ($ranges as $rangeId => $range) {

            DB::table('temp_examiner_assigned_details')
                ->where('user_id', $context->userId)
                ->where('year_id', $context->yearId)
                ->where('month_id', $context->monthId)
                ->where('scheme_id', $context->schemeId)
                ->where('degree_id', $context->degreeId)
                ->where('batch_range_id', $rangeId)
                ->update([
                    'from_date' => $range->from_date,
                    'to_date'   => $range->to_date,
                    'updated_at'=> now(),
                ]);
        }
    }

    public function syncRangeReplacement(
        int $userId,
        int $yearId,
        int $monthId,
        int $schemeId,
        int $degreeId,
        int $oldRangeId,
        int $newRangeId,
        string $newFromDate
    ): void {

        DB::transaction(function () use (
            $userId,$yearId,$monthId,$schemeId,$degreeId,
            $oldRangeId,$newRangeId,$newFromDate
        ) {

            // Prevent double clone
            $exists = DB::table('temp_examiner_assigned_details')
                ->where('user_id',$userId)
                ->where('year_id',$yearId)
                ->where('month_id',$monthId)
                ->where('scheme_id',$schemeId)
                ->where('degree_id',$degreeId)
                ->where('batch_range_id',$newRangeId)
                ->exists();

            if ($exists) return;

            // Clone
            $rows = DB::table('temp_examiner_assigned_details')
                ->where('user_id',$userId)
                ->where('year_id',$yearId)
                ->where('month_id',$monthId)
                ->where('scheme_id',$schemeId)
                ->where('degree_id',$degreeId)
                ->where('batch_range_id',$oldRangeId)
                ->get();

            foreach ($rows as $r) {

                DB::table('temp_examiner_assigned_details')->insert([
                    'user_id'=>$r->user_id,
                    'year_id'=>$r->year_id,
                    'month_id'=>$r->month_id,
                    'scheme_id'=>$r->scheme_id,
                    'degree_id'=>$r->degree_id,
                    'batch_id'=>$r->batch_id,

                    'batch_range_id'=>$newRangeId,

                    'examiner_id'=>$r->examiner_id,
                    'examiner_name'=>$r->examiner_name,
                    'examiner_type'=>$r->examiner_type,
                    'examiner_type_id'=>$r->examiner_type_id,
                    'mobile'=>$r->mobile,

                    'centre_id'=>$r->centre_id,
                    'centre_name'=>$r->centre_name,
                    'attached_id'=>$r->attached_id,
                    'user_college_id'=>$r->user_college_id,

                    'subject_id'=>$r->subject_id,
                    'subject_name'=>$r->subject_name,
                    'user_stream_id'=>$r->user_stream_id,

                    'status'=>$r->status,
                    'status_label'=>$r->status_label,

                    'batch_name'=>$r->batch_name,

                    'from_date'=>$newFromDate,
                    'to_date'=>$newFromDate,

                    'original_from_date'=>$r->from_date,
                    'is_rescheduled' => 1,
                    'rescheduled_at'=>now(),
                    'rescheduled_by'=>$userId,

                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            // Mark old rows inactive
            DB::table('temp_examiner_assigned_details')
                ->where('batch_range_id',$oldRangeId)
                ->update([
                    'status'=>2, // INACTIVE
                    'updated_at'=>now()
                ]);

        });
    }

}
