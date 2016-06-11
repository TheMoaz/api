<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function show($id)
    {
        $user = null; 

        // Search by User ID
        $user = User::with('skills')->find($id);

        if (!$user)
        {
            $content = array('message' => 'Not Found'); 
            return  response($content, 404);
        }
        
        return response()->json($user);
    }

    public function create(Request $request)
    {
        return $request->input(); 
    }
}
