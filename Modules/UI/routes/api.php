<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\Auth\AuthController;
use Modules\Core\Http\Controllers\Api\SettingController;

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    |  Auth
    |--------------------------------------------------------------------------
    |
    */
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('{id}/verify-otp/{otp}', 'verifyOtp')->whereUlid('id');
        Route::post('refresh', 'refresh')->middleware('auth-user');
        Route::post('logout', 'logout')->middleware('auth-user');
        Route::post('forgot-password', 'forgotPassword');
        Route::post('reset-password/{token}', 'resetPassword')->middleware('guest')->name('password.reset');
        Route::get('me', 'me')->middleware('auth-user');
    });

    /*
    |--------------------------------------------------------------------------
    |  Settings
    |--------------------------------------------------------------------------
    |
    */
    Route::middleware(['auth-user', 'role:super_admin'])->prefix('settings')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::patch('/{id}', 'restore');
        Route::delete('/{id}/force', 'forceDelete');
    });
});
