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

if (! function_exists('generateOtp')) {
    function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
