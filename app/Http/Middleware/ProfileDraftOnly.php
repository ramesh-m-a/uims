<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\ProfileStatus;

class ProfileDraftOnly
{
    public function handle($request, Closure $next)
    {
        if (ProfileStatus::forUser(auth()->user()) !== 'draft') {
            abort(403, 'Profile is locked');
        }

        return $next($request);
    }
}
