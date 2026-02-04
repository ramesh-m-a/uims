<?php

use App\Http\Middleware\ProfileDraftOnly;
use App\Http\Middleware\ProfileEditableOnly;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\ForcePasswordChange;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up',)->withMiddleware(function (Middleware $middleware): void {

        // ----------------------------
        // ALIASES (keep as-is)
        // ----------------------------
        $middleware->alias([
            'permission' => CheckPermission::class,
            'has.role' => EnsureUserHasRole::class,
            'profile.draft.only' => ProfileDraftOnly::class,
            'profile.editable' => ProfileEditableOnly::class,
            'force.password.change' => ForcePasswordChange::class,
            ]);

        // ----------------------------
        // Attach globally to web
        // ----------------------------
        $middleware->web(append: [ForcePasswordChange::class,]);
    })->withExceptions(function (Exceptions $exceptions): void {
    /* $exceptions->render(function (HttpExceptionInterface $e, $request) {

        return response()->json([
            'status'     => $e->getStatusCode(),
            'message'    => $e->getMessage(),
            'route'      => $request->route()?->getName(),
            'middleware' => $request->route()?->gatherMiddleware(),
            'user_id'    => auth()->id(),
            'roles'      => auth()->user()?->roles?->pluck('name'),
        ], $e->getStatusCode());

    });*/
    })->create();
