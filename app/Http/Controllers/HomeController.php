<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class HomeController extends Controller
{
    public function index()
    {
    	return "Hello World"; 
    }
}
