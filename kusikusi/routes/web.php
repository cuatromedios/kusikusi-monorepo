<?php

use Illuminate\Support\Facades\Route;
use Kusikusi\Http\Controllers\WebsiteController;
use Kusikusi\Http\Controllers\MediumController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Media
$storage_path = (parse_url(Config::get('filesystems.disks.'.Config::get('kusikusi_media.static_storage.drive').'.url'), PHP_URL_PATH));
Route::get($storage_path.'/'.Config::get('kusikusi_media.prefix', 'media').'/{entity_id}/{preset}/{friendly?}', [MediumController::class, 'get']);
// Website
Route::get('/{path}', [WebsiteController::class, 'any'])->where('path', '.*');