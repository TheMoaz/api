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

$app->get('/', 					'HomeController@index');
$app->get('/users',				'UserController@index');

$app->get('/user/create',		'UserController@create');
$app->get('/user/{id}',			'UserController@show');
$app->get('/user/edit/{id}',	'UserController@edit');

