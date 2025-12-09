<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Traits\ResponseJson;
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
        if (sanctumUser()?->hasAnyRole($roles)) {
            return $next($request);
        }

        return $this->respondError('Permission Denied', 403);
    }
}
