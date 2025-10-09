<?php

use Tymon\JWTAuth\JWTGuard;

if (! function_exists('jwtGuard')) {
    /**
     * @method \Tymon\JWTAuth\Contracts\JWTSubject|null authenticate()
     * @method \Tymon\JWTAuth\Payload parseToken()
     * @method \Tymon\JWTAuth\Payload getPayload()
     *
     * @return JWTGuard
     */
    function jwtGuard()
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        return $guard;
    }
}
