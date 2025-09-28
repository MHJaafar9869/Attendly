<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Auth\AuthController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('refresh', 'refresh');
        Route::post('verify-otp/{otp}/{id}', 'verifyOtp')->whereUlid('id');
        Route::post('logout', 'logout')->middleware('auth:api');
        Route::get('me', 'me')->middleware('auth:api');
    });
});
