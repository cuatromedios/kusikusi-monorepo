<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\EntityRelationController;

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
Route::get('contents/create/{entity_id}', [ContentController::class, 'create']);
Route::resource('contents', ContentController::class);
Route::get('entities-relations/create/{entity_id}', [EntityRelationController::class, 'create']);
Route::resource('entities-relations', EntityRelationController::class);
