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

$app->group(['namespace' => 'App\Http\Controllers', 'prefix' => 'v1'], function () use ($app) {

	$app->post(	'users/add',		'UserController@store');
	$app->patch('users/verify',		'UserController@verify');
	$app->put(	'users/{id}/edit', 	'UserController@update');
	$app->get(	'users/{id}', 		'UserController@show');
	$app->get(	'users', 			'UserController@index');

	$app->post(	'profiles/add',			'ProfileController@store');
	$app->patch('profiles/verify',		'ProfileController@verify');
	$app->put(	'profiles/{id}/edit', 	'ProfileController@update');
	$app->get(	'profiles/{id}', 		'ProfileController@show');
	$app->get(	'profiles', 				'ProfileController@index');

	$app->post(	'merchants/add',			'MerchantController@store');
	$app->patch('merchants/verify',		'MerchantController@verify');
	$app->put(	'merchants/{id}/edit', 	'MerchantController@update');
	$app->get(	'merchants/{id}', 		'MerchantController@show');
	$app->get(	'merchants', 			'MerchantController@index');

});