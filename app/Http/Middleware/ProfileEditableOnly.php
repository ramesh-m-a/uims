<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserProfileDraft;
use App\Support\ProfileStatus;

class ProfileEditableOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        $draft = UserProfileDraft::where('user_id', $user->id)->first();

        // No draft yet → allow (wizard will create it)
        if (! $draft) {
            return $next($request);
        }

        Log::info('PROFILE EDIT CHECK', [
            'draft_id' => $draft->id,
            'status_id' => $draft->status_id,
            'code' => $draft->status?->mas_status_code,
            'label' => $draft->status?->mas_status_name,
        ]);

        if (ProfileStatus::isLocked($draft)) {
            abort(403, 'Profile is locked');
        }

        return $next($request);
    }
}
