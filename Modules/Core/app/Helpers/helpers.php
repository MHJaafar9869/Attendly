<?php

if (! function_exists('jwtGuard')) {
    /**
     * @method \Tymon\JWTAuth\Contracts\JWTSubject|null authenticate()
     * @method \Tymon\JWTAuth\Payload parseToken()
     * @method \Tymon\JWTAuth\Payload getPayload()
     *
     * @return \Tymon\JWTAuth\JWTGuard
     */
    function jwtGuard()
    {
        return auth('api');
    }
}
