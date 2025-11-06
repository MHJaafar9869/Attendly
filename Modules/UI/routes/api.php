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
        Route::post('{user}/verify-otp/{otp}', 'verifyOtp')->whereUlid('user');
        Route::post('forgot-password', 'forgotPassword');
        Route::post('reset-password/{token}', 'resetPassword')->name('password.reset');
        Route::middleware('auth-user')->group(function () {
            Route::post('refresh', 'refresh');
            Route::post('logout', 'logout');
            Route::get('me', 'me');
            Route::post('{user}/upload-image', 'storeProfileImage')->whereUlid('user');
        });
    });

    /*
    |--------------------------------------------------------------------------
    |  Admin
    |--------------------------------------------------------------------------
    |
    */

    Route::prefix('admin')->group(function () {
        Route::prefix('students')->middleware(['auth-user', 'role:super_admin,admin'])->group(function () {
            Route::get('/', fn () => 'hello-students')->middleware('permission:view_students');
        });

        Route::prefix('teachers')->middleware(['auth-user', 'role:super_admin,admin'])->group(function () {
            Route::get('/', fn () => 'hello-teachers')->middleware('permission:view_teachers');
        });
    });

    /*
    |--------------------------------------------------------------------------
    |  Settings
    |--------------------------------------------------------------------------
    |
    */

    Route::middleware(['auth-user', 'role:super_admin,admin'])->prefix('settings')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->middleware('permission:view_settings');
        Route::get('/{id}', 'show')->middleware('permission:view_settings');
        Route::post('/', 'store')->middleware('permission:create_settings');
        Route::put('/{id}', 'update')->middleware('permission:update_settings');
        Route::delete('/{id}', 'destroy')->middleware('permission:delete_settings');
        Route::patch('/{id}', 'restore')->middleware('permission:restore_settings');
        Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_settings');
    });
});
