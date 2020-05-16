<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
*/
$router->group(["prefix" => "api"], function () use ($router) {

    /**
     * Kusikusi Api Routes
     *
     * All these routes point to Kusikusi controllers. Feel free to disable the ones you don't need
     */
    $router->group(['namespace' => 'Kusikusi\Http\Controllers' ], function () use ($router) {
        // Unauthenticated routes
        $router->get('/', function () use ($router) {return ["version" => '4.0'];});
        $router->get('/cms/config', ['uses' => 'CmsController@showConfig']);
        $router->post('/user/login', ['uses' => 'UserController@authenticate']);

        // Authenticated routes
        $router->group(['middleware' => 'auth'], function () use ($router) {
            $router->get('/user/me', ['uses' => 'UserController@showMe']);
            $router->get('/entities[/{model_name}]', ['uses' => 'EntityController@index']);
            $router->patch('/entities/relations/reorder', ['uses' => 'EntityController@reorderRelations']);
            $router->get('/entity/{entity_id}', ['uses' => 'EntityController@show']);
            $router->post('/entity', ['uses' => 'EntityController@create']);
            $router->patch('/entity/{entity_id}', ['uses' => 'EntityController@update']);
            $router->delete('/entity/{entity_id}', ['uses' => 'EntityController@delete']);
            $router->post('/entity/{caller_entity_id}/relation', ['uses' => 'EntityController@createRelation']);
            $router->delete('/entity/{caller_entity_id}/relation/{called_entity_id}/{kind}', ['uses' => 'EntityController@deleteRelation']);
            $router->post('/medium/{entity_id}/upload', ['uses' => 'MediaController@upload']);
            $router->post('/entity/{caller_entity_id}/create_and_relate', ['uses' => 'EntityController@createAndAddRelation']);
        });
    });

    /**
     * Your API routes
     *
     * Add here the routes you need to add to the API, for example special endpoints for you application,
     * note the route is nested under the /api route, so the access to these endpoins would be /api/app/my-endpoint for example
     */
    $router->group(['prefix' => 'app', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers'], function () use ($router) {
        // $router->get('/my-endpoint', ['uses' => 'ExampleController@myMethod']);
    });

    $router->get('/{path:.*}', function () use ($router) {
        return response(['error'=>'Api route not found'], 404);
    });
});

