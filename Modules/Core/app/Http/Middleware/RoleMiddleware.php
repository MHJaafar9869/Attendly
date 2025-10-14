<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $payload = jwtGuard()->getPayload();
        $userRoles = $payload->get('roles') ?? [];

        if (collect($userRoles)->intersect($roles)->isEmpty()) {
            return $this->respondError('Permission Denied', 403);
        }

        return $next($request);
    }
}
