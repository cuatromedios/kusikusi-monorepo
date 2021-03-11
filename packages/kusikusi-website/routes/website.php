<?php

/*
|--------------------------------------------------------------------------
| Waebsite Routes
|--------------------------------------------------------------------------
|
| This should be the last route to be called
|
*/

$router->get('/{path:.*}', 'Kusikusi\Http\Controllers\WebController@any');
