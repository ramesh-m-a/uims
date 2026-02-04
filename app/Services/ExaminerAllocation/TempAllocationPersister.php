<?php

namespace App\Services\ExaminerAllocation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TempAllocationPersister
{
    public function persist(
        int $userId,
        int $yearId,
        int $monthId,
        int $schemeId,
        int $degreeId,
        Collection $rows
    ): void {
        DB::transaction(function () use ($userId, $yearId, $monthId, $schemeId, $degreeId, $rows) {

            DB::table('temp_examiner_assigned_details')
                ->where('user_id', $userId)
                ->where('year_id', $yearId)
                ->where('month_id', $monthId)
                ->where('scheme_id', $schemeId)
                ->where('degree_id', $degreeId)
                ->delete();

            $now = now();
            $buffer = [];

            foreach ($rows as $r) {
                $eid = (int)$r->examinerId;

                [$status, $label] = $eid < 0
                    ? [27, 'Assignment Pending ']
                    : [26, 'Assigned'];

                $buffer[] = [
                    'user_id'         => $userId,
                    'year_id'         => $yearId,
                    'month_id'        => $monthId,
                    'scheme_id'       => $schemeId,
                    'degree_id'       => $degreeId,

                    'batch_id'        => (int)$r->batchId,
                    'batch_range_id'  => (int)$r->rangeId,

                    'examiner_id'     => $eid,
                    'examiner_name'   => (string)$r->name,
                    'examiner_type'   => (string)$r->examinerType,
                    'examiner_type_id'=> (int)$r->examinerTypeId,
                    'mobile'          => (string)$r->mobile,

                    'centre_id'       => (int)$r->centreId,
                    'centre_name'     => (string)$r->centreName,

                    'subject_id'      => (int)$r->subjectId,
                    'subject_name'    => (string)$r->subjectName,

                    'status'          => $status,
                    'status_label'    => $label,

                    'batch_name'      => (string)$r->batchName,
                    'from_date'       => $r->fromDate,
                    'to_date'         => $r->toDate,
                    'label_colour'    => $r->colour ?? '#6c757d',

                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];
            }

            foreach (array_chunk($buffer, 1000) as $chunk) {
                DB::table('temp_examiner_assigned_details')->insert($chunk);
            }
        });
    }
}
