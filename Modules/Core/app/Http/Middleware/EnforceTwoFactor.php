<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Traits\ResponseJson;

class EnforceTwoFactor
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $payload = jwtGuard()->payload();

        if (! $user->two_factor_secret) {
            return $next($request);
        }

        $amr = $payload->get('amr') ?? [];

        if (! in_array('mfa', $amr)) {
            return $this->respondError('Two-factor authentication required', 403);
        }

        return $next($request);
    }
}
