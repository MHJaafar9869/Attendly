<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Traits\ResponseJson;
use Symfony\Component\HttpFoundation\Response;

class MultiAuthMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, array ...$guards): Response
    {
        if ($guards === []) {
            $guards = ['api'];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::shouldUse($guard);

                return $next($request);
            }
        }

        return $this->respondError('Unauthorized', 401);
    }
}
