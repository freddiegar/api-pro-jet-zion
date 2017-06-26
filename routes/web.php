<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the config for an application.
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
    'middleware' => 'throttle:5,30',
], function () use ($app) {
    $app->get('/', ['as' => '/']);
    $app->post('login', ['as' => 'api.login', 'uses' => 'LoginController@login']);
});

$app->group([
    'prefix' => $prefix,
    'middleware' => 'auth:api|throttle',
], function () use ($app) {
    resource($app, 'roles', 'RoleController');
    $app->get("roles/{id}/{relationship}", ['as' => "api.roles.relationship", 'uses' => "RoleController@relationship"]);
    resource($app, 'permissions', 'PermissionController');
    resource($app, 'role-permissions', 'RolePermissionController');
    resource($app, 'users', 'UserController');
    $app->get("users/{id}/{relationship}", ['as' => "api.users.relationship", 'uses' => "UserController@relationship"]);
});
