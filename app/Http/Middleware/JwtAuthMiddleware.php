<?php

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = jwtGuard()->parseToken()->getPayload();
            $user = jwtGuard()->parseToken()->authenticate();

            if (! $user) {
                return $this->respondError('Unauthorized', 401);
            }

            if ($payload->get('token_version') !== $user->token_version) {
                return $this->respondError('Token expired due to role/permission change', 401);
            }
            auth()->setUser($user);
        } catch (\Exception $e) {
            return $this->respondError('Unauthorized', 401);
        }

        return $next($request);
    }
}
