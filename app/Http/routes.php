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

$app->get(	'/', 'HomeController@index');
//
// Authentication routes; all requests routed to the AuthController
//
$app->group(['namespace' => 'App\Http\Controllers', 'prefix' => 'auth'], function () use ($app) 
{
	$app->post(	'login', 	'AuthController@login');
	$app->post(	'pwdhash', 	'AuthController@pwdhash'); // This is a utility function; returns password hash only
	//
	// These routes are authenticated and rate-limited
	//
	$app->get(	'user', 	['middleware' => ['auth:api', 'throttle'], 'uses' => 'AuthController@show']);
	$app->get(	'reset', 	['middleware' => ['auth:api', 'throttle'], 'uses' => 'AuthController@reset']);
	$app->get(	'logout', 	['middleware' => ['auth:api', 'throttle'], 'uses' => 'AuthController@logout']);

	$app->put(	'role', 	['middleware' => ['auth:api', 'throttle'], 'uses' => 'AuthController@role']);
}); 
//
// API routes; all authenticated and rate-limited
//
$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:api', 'throttle'], 'prefix' => 'v1'], function () use ($app) 
{
	$app->post(	'members/create',			'MemberController@create');		// Submit add request
	$app->get(	'members/{id}/activity',	'MemberController@activity');	// Return user activity
	$app->post(	'members/{id}/verify',		'MemberController@verify');		// Verify authorization code
	$app->get(	'members/{id}/edit', 		'MemberController@edit'); 		// Request edit authorization code
	$app->post(	'members/{id}/edit', 		'MemberController@update');		// Submit edit request
	$app->get(	'members/{id}', 			'MemberController@show');		// Fetch individual member record
	$app->get(	'members', 					'MemberController@index');		// Fetch all member records

	$app->post(	'merchants/create',			'MerchantController@create');
	$app->get(	'merchants/{id}/activity', 	'MerchantController@activity');
	//$app->put(	'merchants/{id}/edit', 		'MerchantController@update');	
	$app->get(	'merchants/{id}', 			'MerchantController@show');		
	$app->get(	'merchants', 				'MerchantController@index');	

	$app->get(	'providers', 				'ProviderController@index');
	$app->get(	'providers/{id}', 			'ProviderController@show');
	$app->get(	'providers/{id}/activity', 	'ProviderController@activity');
});

$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:api', 'throttle'], 'prefix' => 'logs'], function () use ($app) 
{
	$app->get(	'/', 				'LogController@index');
	$app->get(	'{id}', 			'LogController@getUserLog');
});
