<?php

use Kusikusi\Http\Controllers\MediumController;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
|
*/
Route::get('/'.Config::get('kusikusi_media.prefix', 'media').'/{entity_id}/{preset}/{friendly?}', [MediumController::class, 'get']);