<?php

namespace App\Services\Exam\College;

use Illuminate\Support\Facades\DB;
use App\Constants\Exam\TempExaminerStatus;

class CollegeRequestCreateService
{
    public static function create(array $data, int $collegeUserId): int
    {
        return DB::transaction(function () use ($data, $collegeUserId) {

            /**
             * STEP 1 — Validate TEMP Row Exists & Is Approved
             */
            $temp = DB::table('temp_examiner_assigned_details')
                ->where('year_id', $data['year_id'])
                ->where('month_id', $data['month_id'])
                ->where('batch_id', $data['batch_id'])
                ->where('batch_range_id', $data['batch_range_id'])
                ->where('examiner_id', $data['examiner_id'])
                ->where('user_college_id', $data['college_id'])
                ->lockForUpdate()
                ->first();

            if (!$temp) {
                throw new \Exception("Allocation not found for this college");
            }

            if ($temp->status != TempExaminerStatus::EXAMINER_APPROVED) {
                throw new \Exception("Request allowed only for approved examiner allocation");
            }

            /**
             * STEP 2 — Insert Request Row
             */
            $requestId = DB::table('college_examiner_request_details')->insertGetId([

                'examiner_college_request_modification_details_year_id' => $data['year_id'],
                'examiner_college_request_modification_details_month_id' => $data['month_id'],
                'examiner_college_request_modification_details_batch_id' => $data['batch_id'],
                'examiner_college_request_modification_details_batch_range_id' => $data['batch_range_id'],
                'examiner_college_request_modification_details_examiner_id' => $data['examiner_id'],
                'examiner_college_request_modification_details_reason_id' => $data['reason_id'],

                'examiner_college_request_modification_details_new_examiner_id'
                => $data['new_examiner_id'] ?? null,

                'examiner_college_request_modification_details_new_from_date'
                => $data['new_from_date'] ?? null,

                'examiner_college_request_modification_details_new_to_date'
                => $data['new_to_date'] ?? null,

                'examiner_college_request_modification_details_comments'
                => $data['comments'] ?? null,

                'examiner_college_request_modification_details_college_id'
                => $data['college_id'],

                'examiner_college_request_modification_details_stream_id'
                => $data['stream_id'] ?? 0,

                'examiner_college_request_modification_details_revised_scheme_id'
                => $data['revised_scheme_id'] ?? 0,

                'created_by' => $collegeUserId,
                'updated_by' => $collegeUserId,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            /**
             * STEP 3 — Update TEMP Status → College Requested
             */
            DB::table('temp_examiner_assigned_details')
                ->where('id', $temp->id)
                ->update([
                    'status' => TempExaminerStatus::COLLEGE_CHANGE_REQUEST_RAISED,
                    'status_label' => 'Change Request of Examiner BY COLLEGE',
                    'updated_at' => now(),
                ]);

            return $requestId;
        });
    }
}
