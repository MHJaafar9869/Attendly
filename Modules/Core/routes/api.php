<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\Auth\AuthController;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    |  Auth
    |--------------------------------------------------------------------------
    |
   \*/
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('{id}/verify-otp/{otp}', 'verifyOtp')->whereUlid('id');
        Route::post('refresh', 'refresh')->middleware('auth-user');
        Route::post('logout', 'logout')->middleware('auth-user');
        Route::post('forgot-password', 'forgotPassword');
        Route::post('reset-password', 'resetPassword')->middleware('auth-user');
        Route::get('me', 'me')->middleware('auth-user');
    });
});
