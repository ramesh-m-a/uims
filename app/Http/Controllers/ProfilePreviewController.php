<?php

namespace App\Http\Controllers;

use App\Models\UserProfileDraft;
use App\Models\BasicDetail;
use App\Support\ProfileStatus;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;
use App\Support\AuditLogger;

class ProfilePreviewController extends Controller
{
    public function show()
    {
        $userId = Auth::id();

        // Draft (for workflow, remarks, etc)
        $draft = UserProfileDraft::where('user_id', $userId)->first();

        // ✅ Committed profile (source of truth for UI)
        $profile = BasicDetail::with([
            'department',   // assumes relation exists
            'college',       // if exists
            'designation',   // if exists
        ])
            ->where('basic_details_user_id', $userId)
            ->first();

        // Latest Principal Rejection (if any)
        $principalRemark = null;

        if ($draft) {
            $principalRemark = AuditLog::where('table_name', 'user_profile_drafts')
                ->where('record_id', $draft->id)
                ->where('action', 'principal_rejected')
                ->latest()
                ->first();
        }

        return view('profile.preview', [
            'draft'           => $draft,
            'profile'         => $profile,   // ✅ use this in blade
            'principalRemark' => $principalRemark,
        ]);
    }

    public function submit()
    {
        $draft = UserProfileDraft::where('user_id', Auth::id())
            ->firstOrFail();

        if (! ProfileStatus::canSubmit($draft)) {
            abort(403, 'Profile cannot be submitted');
        }

        app(\App\Services\Profile\ProfileCommitService::class)
            ->commit(Auth::user(), $draft);

        AuditLogger::log(
            'user_profile_drafts',
            $draft->id,
            'submit'
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profile submitted for approval');
    }
}
