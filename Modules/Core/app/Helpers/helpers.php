<?php

if (! function_exists('jwtGuard')) {
    /**
     * @method \Tymon\JWTAuth\Contracts\JWTSubject|null authenticate()
     * @method \Tymon\JWTAuth\Payload parseToken()
     * @method \Tymon\JWTAuth\Payload getPayload()
     */
    function jwtGuard(): \Tymon\JWTAuth\JWTGuard
    {
        return auth('api');
    }
}
