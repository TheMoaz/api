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

$app->post('login', 'AuthController@postLogin');
$app->post('logout', 'AuthController@postLogout');
$app->post('pwdhash', 'AuthController@postPwdHash');

$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:api', 'history'], 'prefix' => 'v1'], function () use ($app) {

	$app->post(	'users/add',			'UserController@store');	// Submit add request
	$app->post(	'users/{id}/verify',	'UserController@verify');	// Verify authorization code
	$app->get(	'users/{id}/edit', 		'UserController@edit'); 	// Request edit authorization code
	$app->post(	'users/{id}/edit', 		'UserController@update');	// Submit edit request
	$app->get(	'users/{id}', 			'UserController@show');		// Request individual record
	$app->get(	'users', 				'UserController@index');	// Request all records

	$app->post(	'profiles/add',			'ProfileController@store');
	$app->post(	'profiles/verify',		'ProfileController@verify');
	$app->put(	'profiles/{id}/edit', 	'ProfileController@update');
	$app->get(	'profiles/{id}', 		'ProfileController@show');
	$app->get(	'profiles', 			'ProfileController@index');

	$app->post(	'merchants/add',		'MerchantController@store');
	$app->post(	'merchants/verify',		'MerchantController@verify');
	$app->put(	'merchants/{id}/edit', 	'MerchantController@update');
	$app->get(	'merchants/{id}', 		'MerchantController@show');
	$app->get(	'merchants', 			'MerchantController@index');

});

$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth', 'history'], 'prefix' => 'v2'], function () use ($app) {

});