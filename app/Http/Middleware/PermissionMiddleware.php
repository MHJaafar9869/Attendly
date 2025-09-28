<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $payload = jwtGuard()->getPayload();
        $userPermissions = $payload->get('permissions') ?? [];

        if (! in_array($permission, $userPermissions, true)) {
            return $this->respondError('Permission Denied', 403);
        }

        return $next($request);
    }
}
