<?php

use Kusikusi\Http\Controllers\MediumController;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
|
*/

// API Routes
Route::post('/media/{entity_id}/upload', [MediumController::class, 'upload']);
Route::delete('/media/{entity_id}/static/{preset?}', [MediumController::class, 'clearStatic']);