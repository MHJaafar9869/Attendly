<?php

declare(strict_types=1);

namespace Modules\Core\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;

class EnforceTwoFactor
{
    use ResponseJson;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()?->two_factor_secret && ! session('2fa_passed')) {
            return $this->respondError('Please confirm two factor authentication', 401);
        }

        return $next($request);

    }
}
