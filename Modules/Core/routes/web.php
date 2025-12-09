<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\Auth\AuthController;

Route::middleware(['auth', 'verified'])->group(function () {
    //
});
Route::get('reset-password/{token}', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
