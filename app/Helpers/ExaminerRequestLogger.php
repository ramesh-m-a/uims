<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ExaminerRequestLogger
{
    /**
     * Log examiner request action
     *
     * @param int $requestId
     * @param string $action CREATED / APPROVED / REJECTED / UPDATED
     * @param string|null $remarks
     * @param int|null $userId
     * @return void
     */
    public static function log(
        int $requestId,
        string $action,
        ?string $remarks = null,
        ?int $userId = null
    ): void
    {
        try {

            DB::table('examiner_request_action_logs')->insert([
                'request_id' => $requestId,
                'action' => strtoupper($action),
                'action_by' => $userId ?? Auth::id() ?? 0,
                'action_at' => now(),
                'remarks' => $remarks,
                'ip_address' => Request::ip()
            ]);

        } catch (\Throwable $e) {

            // IMPORTANT: Never break main business flow due to logging failure
            \Log::error('ExaminerRequestLogger Failed', [
                'request_id' => $requestId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }
}
