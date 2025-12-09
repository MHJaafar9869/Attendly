<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\Auth\AuthController;
use Modules\Core\Http\Controllers\Api\Setting\SettingController;
use Modules\Core\Http\Controllers\Api\User\UserController;
use Modules\Domain\Http\Controllers\Api\ClassroomController;
use Modules\Domain\Http\Controllers\Api\Student\StudentController;

Route::prefix('v1')->group(function () {
    // Test Route: /api/v1/test
    Route::get('/test', fn () => response()->json(['message' => 'This is a test']));

    /*
    |--------------------------------------------------------------------------
    |  Auth
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('{user:slug}/verify-otp', 'verifyOtp');
        Route::post('forgot-password', 'forgotPassword');
        Route::post('reset-password/{token}', 'resetPassword')->name('password.reset');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', 'logout');
            Route::get('me', 'me');
            Route::post('{user:slug}/upload-image', 'storeUserImage');
        });
    });

    /*
    |--------------------------------------------------------------------------
    |  Routes where authentication is required
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'verified-email'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        |  User
        |--------------------------------------------------------------------------
        */

        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->middleware('permission:view_users');
            Route::get('/{id}', 'show')->middleware('permission:view_users');
            Route::post('/', 'store')->middleware('permission:create_users');
            Route::put('/{id}', 'update')->middleware('permission:update_users');
            Route::delete('/{id}', 'destroy')->middleware('permission:delete_users');
            Route::patch('/{id}/restore', 'restore')->middleware('permission:restore_users');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_users');
        });

        /*
        |--------------------------------------------------------------------------
        |  Student
        |--------------------------------------------------------------------------
        */

        Route::prefix('students')->middleware('role:super_admin,admin')->controller(StudentController::class)->group(function () {
            Route::get('/', 'index')->middleware('permission:view_students');
            Route::get('/{id}', 'show')->middleware('permission:view_students');
            Route::post('/', 'store')->middleware('permission:create_students');
            Route::put('/{id}', 'update')->middleware('permission:update_students');
            Route::delete('/{id}', 'destroy')->middleware('permission:delete_students');
            Route::patch('/{id}/restore', 'restore')->middleware('permission:restore_students');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_students');
            Route::get('/search', 'searchStudents')->middleware('permission:view_students');
        });

        /*
        |--------------------------------------------------------------------------
        |  Classroom
        |--------------------------------------------------------------------------
        */

        Route::prefix('classrooms')->controller(ClassroomController::class)->group(function () {});

        /*
        |--------------------------------------------------------------------------
        |  Admin
        |--------------------------------------------------------------------------
        */

        Route::prefix('admin')->middleware('role:super_admin,admin')->group(function () {
            Route::prefix('students')->group(function () {
                Route::get('/', fn () => 'hello-students')->middleware('permission:view_students');
            });

            Route::prefix('teachers')->group(function () {
                Route::get('/', fn () => 'hello-teachers')->middleware('permission:view_teachers');
            });
        });

        /*
        |--------------------------------------------------------------------------
        |  Settings
        |--------------------------------------------------------------------------
        */

        Route::middleware('role:super_admin,admin')->prefix('settings')->controller(SettingController::class)->group(function () {
            Route::get('/', 'index')->middleware('permission:view_settings');
            Route::get('/{id}', 'show')->middleware('permission:view_settings');
            Route::post('/', 'store')->middleware('permission:create_settings');
            Route::put('/{id}', 'update')->middleware('permission:update_settings');
            Route::delete('/{id}', 'destroy')->middleware('permission:delete_settings');
            Route::patch('/{id}', 'restore')->middleware('permission:restore_settings');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_settings');
        });
    });
});
