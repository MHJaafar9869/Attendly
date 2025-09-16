<?php

use Illuminate\Support\Facades\Route;
use Modules\Feature\Http\Controllers\FeatureController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('features', FeatureController::class)->names('feature');
});
