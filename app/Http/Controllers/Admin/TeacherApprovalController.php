<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\ProfileApproval;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherApprovalController extends Controller
{
    /**
     * Approve / Reject my-details profile
     */
    public function action(Request $request, User $user)
    {
        $request->validate([
            'action'  => ['required', 'in:approve,reject'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $user) {

            ProfileApproval::create([
                'user_id'          => $user->id,
                'basic_details_id' => optional($user->basicDetails)->id,
                'level'            => 'college', // later: principal / rguhs
                'status'           => $request->action === 'approve'
                    ? 'approved'
                    : 'rejected',
                'remarks'          => $request->remarks,
                'acted_by'         => auth()->id(),
                'acted_at'         => now(),
            ]);

            // 🔐 AUDIT LOG (your custom system)
            AuditLogger::log(
                table: 'profile_approvals',
                recordId: $user->id,
                action: $request->action,
                oldValues: null,
                newValues: [
                    'level'   => 'college',
                    'status'  => $request->action,
                    'remarks' => $request->remarks,
                ]
            );
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Profile action completed successfully.');
    }
}
