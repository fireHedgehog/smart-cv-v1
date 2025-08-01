<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// backend/routes/web.php
$router->get('/db-test', function () {
    try {
        \DB::connection()->getPdo();
        return '✅ DB connected!';
    } catch (\Exception $e) {
        return '❌ DB connection failed: ' . $e->getMessage();
    }
});

$router->post('v1/test-middleware', [
    'uses' => 'CVController@create'
]);
