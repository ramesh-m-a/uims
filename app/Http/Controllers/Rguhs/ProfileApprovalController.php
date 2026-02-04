<?php

namespace App\Http\Controllers\Rguhs;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Support\AuditLogger;
use App\Support\ProfileStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileApprovalController extends Controller
{
    /**
     * RGUHS APPROVE → FINAL APPROVAL
     */
    public function approve(Request $request, User $user)
    {
        DB::transaction(function () use ($user) {

            $oldStatusId = $user->user_status_id;

            $user->update([
                'user_status_id' => ProfileStatus::id('APPROVED'),
                'updated_by'     => auth()->id(),
            ]);

            AuditLogger::log(
                table: 'users',
                recordId: $user->id,
                action: 'rguhs_approved',
                oldValues: [
                    'user_status_id' => $oldStatusId,
                ],
                newValues: [
                    'user_status_id' => $user->user_status_id,
                ],
            );
        });

        return back()->with('success', 'Profile finally approved by RGUHS');
    }

    /**
     * RGUHS REJECT → SEND BACK TO TEACHER
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $user) {

            $oldStatusId = $user->user_status_id;

            $user->update([
                'user_status_id' => ProfileStatus::id('RGUHS_REJECTED'),
                'updated_by'     => auth()->id(),
            ]);

            AuditLogger::log(
                table: 'users',
                recordId: $user->id,
                action: 'rguhs_rejected',
                oldValues: [
                    'user_status_id' => $oldStatusId,
                ],
                newValues: [
                    'user_status_id' => $user->user_status_id,
                    'remarks'        => $request->remarks,
                ],
            );
        });

        return back()->with('error', 'Profile rejected and sent back to my-details');
    }
}
