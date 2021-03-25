<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kusikusi\Http\Controllers\EntityController;
use Kusikusi\Http\Controllers\EntityRelationController;

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

Route::get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/entities', [EntityController::class, 'index']);
Route::get('/entities/{entity_id}', [EntityController::class, 'show']);
Route::post('/entities', [EntityController::class, 'store']);
Route::patch('/entities/{entity_id}', [EntityController::class, 'update']);
Route::delete('/entities/{entity_id}', [EntityController::class, 'destroy']);
Route::patch('/entities/{entity_id}/restore', [EntityController::class, 'restore']);
Route::get('/entities/{entity_id}/relations', [EntityRelationController::class, 'index']);
Route::post('/entities/{entity_id}/relations/{called_entity_id}/{kind}', [EntityRelationController::class, 'store']);
Route::delete('/entities/{entity_id}/relations/{called_entity_id}/{kind}', [EntityRelationController::class, 'destroy']);
Route::delete('/entities/{entity_id}/relations/{relation_id}', [EntityRelationController::class, 'destroy']);
Route::patch('/entities/{entity_id}/relations/reorder', [EntityRelationController::class, 'reorder']);