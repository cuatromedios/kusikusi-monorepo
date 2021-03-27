<?php

use Kusikusi\Http\Controllers\MediumController;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
|
*/

// Web routes
$storage_path = (parse_url(Config::get('filesystems.disks.'.Config::get('kusikusi_media.static_storage.drive').'.url'), PHP_URL_PATH));
Route::get($storage_path.'/'.Config::get('kusikusi_media.prefix', 'media').'/{entity_id}/{preset}/{friendly?}', [MediumController::class, 'get']);