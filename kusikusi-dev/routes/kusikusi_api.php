<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kusikusi\Http\Controllers\EntityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$id_rule='[A-Za-z0-9_-]{1,32}';

Route::get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/entities', [EntityController::class, 'index']);
Route::get('/entities/{entity_id}', [EntityController::class, 'show']);
Route::post('/entities', [EntityController::class, 'store']);
Route::patch('/entities/{entity_id}', [EntityController::class, 'update']);
Route::delete('/entities/{entity_id}', [EntityController::class, 'destroy']);
Route::patch('/entities/{entity_id}/restore', [EntityController::class, 'restore']);
Route::post('/entities/{entity_id}/relation', [EntityController::class, 'restore']);