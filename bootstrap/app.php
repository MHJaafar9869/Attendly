<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\ApiExceptionHandler;
use Modules\Core\Http\Middleware\EnforceTwoFactor;
use Modules\Core\Http\Middleware\JwtAuthMiddleware;
use Modules\Core\Http\Middleware\MultiAuthMiddleware;
use Modules\Core\Http\Middleware\PermissionMiddleware;
use Modules\Core\Http\Middleware\RoleMiddleware;
use Modules\Core\Http\Middleware\TwoFactorDisabled;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Regular Authentication using JWT
            'auth-user' => JwtAuthMiddleware::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'multi-auth' => MultiAuthMiddleware::class,

            // Two Factor Authentication -- TODO
            '2fa_enforced' => EnforceTwoFactor::class,
            '2fa_disabled' => TwoFactorDisabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $th, Request $request) {
            // Handle common api route exceptions
            if ($request->is('api/*')) {
                return (new ApiExceptionHandler)
                    ->handleApiException($th);
            }
        });
        // Render JSON at Client.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => ($request->is('api/*') || $request->ajax()) ? true : false
        );
    })->create();
