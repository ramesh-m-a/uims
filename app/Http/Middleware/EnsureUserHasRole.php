<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        /**
         * ==================================================
         * 🔥 ADMIN BYPASS (FINAL)
         * RULE:
         * user_role_id === NULL => ADMIN => SKIP ALL ROLE CHECKS
         * ==================================================
         */
        if (is_null($user->user_role_id)) {
            return $next($request);
        }

        /**
         * ==================================================
         * NON-ADMIN USERS ONLY
         * ==================================================
         */

        // No roles assigned → pending assignment
        if ($user->roles()->count() === 0) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your account is pending role assignment. Please contact administrator.',
                ]);
        }

        // Specific role required
        if ($role && ! $user->hasRole($role)) {
            abort(403, 'This action is unauthorized');
        }

        return $next($request);
    }
}
