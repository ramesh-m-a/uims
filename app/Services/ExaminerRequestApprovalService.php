<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Helpers\ExaminerRequestLogger;
use Exception;

class ExaminerRequestApprovalService
{
    /**
     * ALIGN WITH YOUR PRODUCTION STATUS MASTER
     */
    private const STATUS_PENDING = 26;
    private const STATUS_APPROVED = 28; // CHANGE IF DIFFERENT IN YOUR DB

    /**
     * Approve Examiner Change Request
     */
    public function approve(int $requestId, int $approvedByUserId): bool
    {
        return DB::transaction(function () use ($requestId, $approvedByUserId) {

            // ------------------------------------------------
            // STEP 1 — Lock Request
            // ------------------------------------------------
            $request = DB::table('college_examiner_request_details')
                ->where('id', $requestId)
                ->lockForUpdate()
                ->first();

            if (!$request) {
                throw new Exception('Request not found');
            }

            // ------------------------------------------------
            // STEP 2 — Validate Pending Status
            // ------------------------------------------------
            if ((int)$request->college_examiner_request_details_status_id !== self::STATUS_PENDING) {
                throw new Exception('Request already processed');
            }

            // ------------------------------------------------
            // STEP 3 — Validate New Examiner Exists
            // ------------------------------------------------
            if (!$request->college_examiner_request_details_new_examiner_id) {
                throw new Exception('New examiner not selected');
            }

            // ------------------------------------------------
            // STEP 4 — Find Temp Assignment (REAL COLUMN NAMES)
            // ------------------------------------------------
            $tempAssignment = DB::table('temp_examiner_assigned_details')
                ->where('examiner_id', $request->college_examiner_request_details_examiner_id)
                ->where('centre_id', $request->college_examiner_request_details_college_id)
            //    ->where('attached_id', $request->college_examiner_request_details_batch_range_id)
                ->lockForUpdate()
                ->first();

            if (!$tempAssignment) {
                throw new Exception('Temp examiner assignment not found');
            }

            // ------------------------------------------------
            // STEP 5 — Apply Examiner Change
            // ------------------------------------------------
            DB::table('temp_examiner_assigned_details')
                ->where('id', $tempAssignment->id)
                ->update([
                    'examiner_id' => $request->college_examiner_request_details_new_examiner_id,
                    'updated_at' => now()
                ]);

            // ------------------------------------------------
            // STEP 6 — Update Request Status
            // ------------------------------------------------
            DB::table('college_examiner_request_details')
                ->where('id', $requestId)
                ->update([
                    'college_examiner_request_details_status_id' => self::STATUS_APPROVED,
                    'updated_by' => $approvedByUserId,
                    'updated_at' => now()
                ]);

            // ------------------------------------------------
            // STEP 7 — Log Action
            // ------------------------------------------------
            ExaminerRequestLogger::log(
                requestId: $requestId,
                action: 'APPROVED',
                remarks: 'Approved via Admin Panel',
                userId: $approvedByUserId
            );

            return true;
        });
    }
}
