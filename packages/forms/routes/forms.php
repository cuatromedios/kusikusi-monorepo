<?php

use Kusikusi\Http\Controllers\FormController;

/*
|--------------------------------------------------------------------------
| Kusikusi Website Routes
|--------------------------------------------------------------------------
|
| The web route that catches all routes.
|
*/

Route::middleware('web')->group( function () {
  Route::post('/form', [FormController::class, 'receive']);
});
