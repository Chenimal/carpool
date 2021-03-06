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

$app->get('/', function () use ($app) {
    return $app->version();
});

// create single order
$app->get('orders/create-random', [
    'uses' => 'OrdersController@createRandom',
]);

// assign orders
$app->get('orders/assign', [
    'uses' => 'OrdersController@assign',
]);

// get 2 vehicles info
$app->get('vehicles/random', [
    'uses' => 'VehiclesController@getRandom',
]);
