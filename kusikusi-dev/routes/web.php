<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\EntityArchiveController;

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

Route::get('/', function () { return view('welcome'); })->name('welcome');
Route::get('/models', function () { return view('models'); })->name('models');
Route::resource('entities', EntityController::class);
Route::get('entities_archives/restore','App\Http\Controllers\EntityArchiveController@restore')->name('entities_archives.restore');
Route::put('entities_archives','App\Http\Controllers\EntityArchiveController@restore_store')->name('entities_archives.restore_store');
Route::resource('entities_archives', EntityArchiveController::class);