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
        return auth('api');
    }
}
