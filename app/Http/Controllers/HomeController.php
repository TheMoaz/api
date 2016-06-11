<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $functions = array(
            'list users' => '/users',
            'show user' => '/users/{id}',
        );
        return response()->json($functions);
    }
}
