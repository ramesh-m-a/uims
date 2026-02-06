<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Helpers\ExaminerRequestLogger;
use Exception;

class ExaminerRequestRejectService
{
    /**
     * ALIGN WITH YOUR PRODUCTION STATUS MASTER
     */
    private const STATUS_PENDING = 26;
    private const STATUS_REJECTED = 28; // CHANGE IF DIFFERENT IN YOUR DB

    /**
     * Reject Examiner Change Request
     */
    public function reject(
        int $requestId,
        int $rejectedByUserId,
        ?string $remarks = 'Rejected via Admin Panel'
    ): bool
    {
        return DB::transaction(function () use ($requestId, $rejectedByUserId, $remarks) {

            // ------------------------------------------------
            // STEP 1 — Lock Request Row
            // ------------------------------------------------
            $request = DB::table('college_examiner_request_details')
                ->where('id', $requestId)
                ->lockForUpdate()
                ->first();

            if (!$request) {
                throw new Exception('Request not found');
            }

            // ------------------------------------------------
            // STEP 2 — Validate Still Pending
            // ------------------------------------------------
            if ((int)$request->college_examiner_request_details_status_id !== self::STATUS_PENDING) {
                throw new Exception('Request already processed');
            }

            // ------------------------------------------------
            // STEP 3 — Update Status → Rejected
            // ------------------------------------------------
            DB::table('college_examiner_request_details')
                ->where('id', $requestId)
                ->update([
                    'college_examiner_request_details_status_id' => self::STATUS_REJECTED,
                    'updated_by' => $rejectedByUserId,
                    'updated_at' => now()
                ]);

            // ------------------------------------------------
            // STEP 4 — Log Action
            // ------------------------------------------------
            ExaminerRequestLogger::log(
                requestId: $requestId,
                action: 'REJECTED',
                remarks: $remarks,
                userId: $rejectedByUserId
            );

            return true;
        });
    }
}
