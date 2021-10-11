<?php

use Kusikusi\Http\Controllers\WebsiteController;

/*
|--------------------------------------------------------------------------
| Kusikusi Website Routes
|--------------------------------------------------------------------------
|
| The web route that catches all routes.
|
*/

Route::middleware('web')
    ->group( function () {
      Route::get('/{path}', [WebsiteController::class, 'any'])->where('path', '.*');
    });
