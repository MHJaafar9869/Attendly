<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Modules\Core\Traits\ResponseJson;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = jwtGuard()->getPayload();

            if (! $user = jwtGuard()->user()) {
                return $this->respondError('Unauthenticated', 401);
            }

            if (! $user->email_verified_at) {
                return $this->respondError('Email is not verified. Please verify your email first');
            }

            if ($payload->get('token_version') !== $user->token_version) {
                return $this->respondError('Token invalidated. Please reauthenticate', 401);
            }

            jwtGuard()->setUser($user);
        } catch (Exception $e) {
            return $this->respondError('Unauthorized', 401);
        }

        return $next($request);
    }
}
