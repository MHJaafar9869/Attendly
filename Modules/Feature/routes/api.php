<?php

use Illuminate\Support\Facades\Route;
use Modules\Feature\Http\Controllers\FeatureController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('features', FeatureController::class)->names('feature');
});
