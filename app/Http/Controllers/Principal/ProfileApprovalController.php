<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Master\Common\Status;
use App\Models\UserProfileDraft;
use Illuminate\Http\Request;

class ProfileApprovalController extends Controller
{
    public function show(User $user)
    {
        $draft = UserProfileDraft::where('user_id', $user->id)
            ->whereNotNull('submitted_at')   // 🔒 only submitted profiles
            ->first();

        abort_if(! $draft, 404);

        return view('principal.profile-preview', [
            'user'  => $user,
            'draft' => $draft,
        ]);
    }

    public function approve(User $user)
    {
        $draft = $user->profileDraft;

        abort_if(! $draft || ! $draft->submitted_at, 404);

        $approvedStatusId = Status::where('mas_status_code', 'Principal Approved')
            ->value('id');

        $draft->update([
            'status_id' => $approvedStatusId,
            'locked_by' => auth()->id(),
            'locked_at' => now(),
        ]);

        return redirect()
            ->route('principal.profiles.show', $user)
            ->with('success', 'Profile approved and forwarded to RGUHS');
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        $draft = $user->profileDraft;

        abort_if(! $draft || ! $draft->submitted_at, 404);

        $rejectedStatusId = Status::where('mas_status_code', 'Principal Rejected')
            ->value('id');

        $draft->update([
            'status_id' => $rejectedStatusId,
            'locked_by' => null,
            'locked_at' => null,
        ]);

        // (Optional) save remarks to audit table later

        return redirect()
            ->route('principal.profiles.show', $user)
            ->with('success', 'Profile rejected and sent back to my-details');
    }
}
