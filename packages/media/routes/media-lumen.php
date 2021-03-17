<?php

use Kusikusi\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Config;

/*
|--------------------------------------------------------------------------
| Media Routes
|--------------------------------------------------------------------------
*/

$router->get('/'.Config::get('kusikusi_media.prefix', 'media').'/{entity_id}/{preset}[/{friendly}]', ['uses' => 'Kusikusi\Http\Controllers\MediumController@get']);
