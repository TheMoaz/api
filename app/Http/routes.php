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
$app->post(	'upload', 'HomeController@upload');
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

	//$app->put(	'role', 	['middleware' => ['auth:api', 'throttle'], 'uses' => 'AuthController@role']);
}); 
//
// API routes; all authenticated and rate-limited
//
$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:api', 'throttle'], 'prefix' => 'v1'], function () use ($app) 
{
	$app->post(		'members/create',					'MemberController@create');		// Submit add request
	$app->get(		'members/{id:[0-9]+}/activity',		'MemberController@activity');	// Return user activity
	$app->post(		'members/{id:[0-9]+}/verify',		'MemberController@verify');		// Verify authorization code
	$app->get(		'members/{id:[0-9]+}/authorize', 	'MemberController@authorize'); 	// Request edit authorization code
	$app->post(		'members/{id:[0-9]+}/edit', 		'MemberController@edit');		// Submit edit request

	$app->get(		'members/{id:[0-9]+}/skills',		'MemberController@getSkills');	// Fetch individual member skills
	$app->put(		'members/{id:[0-9]+}/skills',		'MemberController@putSkill');	// Fetch individual member skills
	$app->delete(	'members/{id:[0-9]+}/skills',		'MemberController@delSkill');	// Fetch individual member skills

	$app->get(		'members/{id:[0-9]+}', 				'MemberController@show');		// Fetch individual member record
	$app->get(		'members', 							'MemberController@index');		// Fetch all member records

	$app->post(		'merchants/create',					'MerchantController@create');
	$app->get(		'merchants/{id:[0-9]+}/activity', 	'MerchantController@activity');
	$app->get(		'merchants/{id:[0-9]+}', 			'MerchantController@show');		
	$app->get(		'merchants', 						'MerchantController@index');	

	$app->get(		'providers', 						'ProviderController@index');
	$app->get(		'providers/{id:[0-9]+}', 			'ProviderController@show');
	$app->get(		'providers/{id:[0-9]+}/activity', 	'ProviderController@activity');

	$app->get(		'skills', 							'SkillController@index');
	$app->post(		'skills/create', 					'SkillController@create');
	$app->get(		'skills/{id:[0-9]+}', 				'SkillController@show');
	$app->put(		'skills/{id:[0-9]+}/edit', 			'SkillController@update');
	
});

//
// API routes; all authenticated and rate-limited
//
$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['throttle'], 'prefix' => 'search'], function () use ($app) 
{
	$app->get(	'/',					'SearchController@index');	
});

$app->group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth:api', 'throttle'], 'prefix' => 'logs'], function () use ($app) 
{
	$app->get(	'/', 				'LogController@index');
	$app->get(	'{id:[0-9]+}', 			'LogController@getUserLog');
});
