<?php

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiAuthMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, array ...$guards): Response
    {
        if (empty($guards)) {
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
