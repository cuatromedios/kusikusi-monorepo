<?php

use Kusikusi\Http\Controllers\MediumController;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Media API Routes
|--------------------------------------------------------------------------
|
*/
Route::prefix('api')
    ->middleware('api')
    ->group( function () {
      // Authenticated
      Route::middleware('auth:sanctum')->group(function () {
        Route::post('/media/{entity_id}/upload', [MediumController::class, 'upload']);
        Route::delete('/media/{entity_id}/static/{preset?}', [MediumController::class, 'clearStatic']);
      });
    });
