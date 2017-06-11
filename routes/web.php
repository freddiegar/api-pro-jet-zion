<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$prefix = 'api/v1';

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group([
    'prefix' => $prefix,
], function () use ($app) {
    $app->post('login', ['as' => 'api.login', 'uses' => 'LoginController@login']);
});

$app->group([
    'prefix' => $prefix,
    'middleware' => ['auth:api'],
], function () use ($app) {
    $app->post('user/create', ['as' => 'api.user.create', 'uses' => 'UserController@create']);
    $app->post('user/read/{id}', ['as' => 'api.user.read', 'uses' => 'UserController@read']);
});
