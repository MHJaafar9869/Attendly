<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Traits\ResponseJson;
use Symfony\Component\HttpFoundation\Response;

class VerifiedEmailMiddleware
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->respondError('Unauthenticated', 401);
        }

        if (! $user->email_verified_at) {
            return $this->respondError(
                'Email is not verified. Please verify your email first'
            );
        }

        return $next($request);
    }
}
