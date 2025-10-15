<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Traits\ResponseJson;

class TwoFactorDisabled
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $user->two_factor_secret) {
            return $this->respondError(`You don't have Two Factor Authentication enabled`);
        }

        return $next($request);
    }
}
