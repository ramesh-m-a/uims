<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        /**
         * ==================================================
         * 🔥 ADMIN BYPASS (FINAL & ABSOLUTE)
         * RULE:
         * user_role_id === NULL => ADMIN => ALLOW EVERYTHING
         * ==================================================
         */
        if (is_null($user->user_role_id)) {
            return $next($request);
        }

        /**
         * ==================================================
         * NON-ADMIN PERMISSION CHECK
         * ==================================================
         */
        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'This action is unauthorized - CheckPermission');
    }
}
