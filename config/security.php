<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP
    |--------------------------------------------------------------------------
    |
    */
    'otp' => [

        'otp_ttl_minutes' => env('OTP_TTL_MINUTES', 10),

        'otp_max_attempts' => env('OTP_MAX_ATTEMPTS', 5),

        'otp_lock_minutes' => env('OTP_LOCK_MINUTES', 15),

    ],

    'sanctum' => [

        'token_expiry_days' => 30,

    ],

];
