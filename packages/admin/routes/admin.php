<?php

use Kusikusi\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware('web')
    ->group( function () {
        Route::get('/admin/{path?}', [AdminController::class, 'any'])->where('path', '.*');
    });
