<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\Admin\AdminController;
use Modules\Core\Http\Controllers\Api\Auth\AuthController;
use Modules\Core\Http\Controllers\Api\Setting\SettingController;
use Modules\Core\Http\Controllers\Api\User\UserController;
use Modules\Domain\Http\Controllers\Api\Classroom\ClassroomController;
use Modules\Domain\Http\Controllers\Api\Student\StudentController;
use Modules\Domain\Http\Controllers\Api\Teacher\TeacherController;

Route::prefix('v1')->group(function () {
    // Test Route: /api/v1/test
    Route::get('/test', fn () => response()->json(['message' => 'Hello World'], 200));

    /*
    |--------------------------------------------------------------------------
    |  Auth
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('teachers/register', 'register');
        Route::post('students/register', 'register');
        Route::post('teachers/{user:slug}/verify-otp', 'verifyOtp');
        Route::post('students/{user:slug}/verify-otp', 'verifyOtp');
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

        Route::apiResource('users', UserController::class)->except(['store', 'update']);
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::patch('/{id}/restore', 'restore')->middleware('permission:restore_users');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_users');
            Route::get('/search', 'searchUsers');
        });

        /*
        |--------------------------------------------------------------------------
        |  Student
        |--------------------------------------------------------------------------
        */

        Route::apiResource('students', StudentController::class);
        Route::prefix('students')->controller(StudentController::class)->group(function () {
            Route::patch('/{id}/restore', 'restore')->middleware('permission:restore_students');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_students');
            Route::get('/search', 'searchStudents');
        });

        /*
        |--------------------------------------------------------------------------
        |  Teacher
        |--------------------------------------------------------------------------
        */

        Route::apiResource('teachers', TeacherController::class);
        Route::prefix('teachers')->controller(TeacherController::class)->group(function () {
            Route::patch('/{id}/restore', 'restore')->middleware('permission:restore_teachers');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_teachers');
            Route::get('/search', 'searchTeachers');
        });

        /*
        |--------------------------------------------------------------------------
        |  Classroom
        |--------------------------------------------------------------------------
        */

        Route::apiResource('classrooms', ClassroomController::class);
        Route::prefix('classrooms')->controller(ClassroomController::class)->group(function () {
            Route::patch('/{id}/restore', 'restore')->middleware('permission:restore_classrooms');
            Route::delete('/{id}/force', 'forceDelete')->middleware('permission:force_delete_classrooms');
            Route::get('/search', 'searchClassrooms');
        });

        /*
        |--------------------------------------------------------------------------
        |  Admin
        |--------------------------------------------------------------------------
        */

        Route::prefix('admin')->controller(AdminController::class)
            ->middleware('role:super_admin,admin')
            ->group(function () {
                /*
                |--------------------------------------------------------------------------
                |  Users
                |--------------------------------------------------------------------------
                */

                Route::get('users/analytics', 'usersAnalytics');

                /*
                |--------------------------------------------------------------------------
                |  Students
                |--------------------------------------------------------------------------
                */

                Route::get('students/analytics', 'studentsAnalytics');

                /*
                |--------------------------------------------------------------------------
                |  Teachers
                |--------------------------------------------------------------------------
                */

                Route::get('teachers/analytics', 'teachersAnalytics');

                /*
                |--------------------------------------------------------------------------
                |  Classrooms
                |--------------------------------------------------------------------------
                */

                Route::get('classrooms/analytics', 'classroomsAnalytics');

                /*
                |--------------------------------------------------------------------------
                |  Settings
                |--------------------------------------------------------------------------
                */

                Route::middleware('role:super_admin')->group(function () {
                    Route::apiResource('settings', SettingController::class);
                    Route::prefix('settings')->controller(SettingController::class)->group(function () {
                        Route::patch('/{id}', 'restore');
                        Route::delete('/{id}/force', 'forceDelete');
                    });
                });
            });
    });
});
