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

    public function read($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    public function create(Request $request)
    {
        $user = User::create($request->all());
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $updated = $user->update($request->all());
        return response()->json(['updated' => $updated]);
    }

    public function delete($id)
    {
        $deletedRows = User::destroy($id);
        $deleted = $deletedRows == 1;
        return response()->json(['deleted' => $deleted]);
    }
}
