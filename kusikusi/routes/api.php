<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Kusikusi\Http\Controllers\MediumController;
use Kusikusi\Http\Controllers\EntityController;
use Kusikusi\Http\Controllers\EntityRelationController;
use Illuminate\Support\Facades\Config;

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
// Route::post('/login', [AuthController::class, 'login']);
Route::get('/cms/config', function() {
    $cms = config('kusikusi_admin');
    return $cms;
});
// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::prefix('entities')->group(function () {
        Route::get('/', [EntityController::class, 'index']);
        Route::get('/{entity_id}', [EntityController::class, 'show']);
        Route::post('/', [EntityController::class, 'store']);
        Route::patch('/{entity_id}', [EntityController::class, 'update']);
        Route::delete('/{entity_id}', [EntityController::class, 'destroy']);
        Route::patch('/{entity_id}/restore', [EntityController::class, 'restore']);
        Route::get('/{entity_id}/relations', [EntityRelationController::class, 'index']);
        Route::post('/{entity_id}/relations/{called_entity_id}/{kind}', [EntityRelationController::class, 'store']);
        Route::delete('/{entity_id}/relations/{called_entity_id}/{kind}', [EntityRelationController::class, 'destroy']);
        Route::delete('/{entity_id}/relations/{relation_id}', [EntityRelationController::class, 'destroy']);
        Route::patch('/{entity_id}/relations/reorder', [EntityRelationController::class, 'reorder']);
    });
    Route::prefix('media')->group(function () {
        Route::post('/{entity_id}/upload', [MediumController::class, 'upload']);
        Route::delete('/{entity_id}/static/{preset?}', [MediumController::class, 'clearStatic']);
    });
});

