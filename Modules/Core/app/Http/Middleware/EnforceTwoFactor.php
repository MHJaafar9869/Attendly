<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Models\User;
use Modules\Core\Traits\ResponseJson;

class EnforceTwoFactor
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var User $user */
        $user = sanctumUser();

        if (! $user->two_factor_secret) {
            return $next($request);
        }

        return $next($request);
    }
}
