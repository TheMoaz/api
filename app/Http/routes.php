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

// $app->get('/', function () use ($app) {
//     return $app->version();
// });

$app->group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function ($app)
{
	$app->get('/', 'HomeController@index');
	$app->get('/users',	'UserController@index');
	$app->post('/users/create',	'UserController@create');
	$app->get('/users/{id}', 'UserController@read');
	$app->put('/users/edit/{id}', 'UserController@update');
	$app->delete('/users/delete/{id}', 'UserController@delete');
});


