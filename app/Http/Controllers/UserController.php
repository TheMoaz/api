<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends Controller
{
	public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
    	return "List of users"; 
    }

    public function show($id)
    {
    	return $id;
    }
}
