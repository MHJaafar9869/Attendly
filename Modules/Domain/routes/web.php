<?php

use Illuminate\Support\Facades\Route;
use Modules\Domain\Http\Controllers\DomainController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('domains', DomainController::class)->names('domain');
});
