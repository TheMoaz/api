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
        //$this->middleware('oauth');
    }

    /**
     * List all users - limited to 10 for now
     *
     * @return object
     */
    public function index(Request $request)
    {
        if ($request->has('page'))
        {
            $users = User::members()->orderBy('created_at', 'desc')->skip($request->page*10)->take(10)->get();
        }
        else
        {
            $users = User::members()->orderBy('created_at', 'desc')->take(10)->get();
        }

        if (count($users)) return response()->json($users);

        return response(array('message'=>'Not Found'), 404);
    }

    /**
     * Return User object with specific user_id
     *
     * @param  int  $id
     * @return object
     */
    public function show($id)
    {
        $user = User::members()->find($id);

        if (count($user)) return response()->json($user);

        return response(array('message'=>'Not Found'), 404);
    }

    /**
     * Create a new merchant (User)
     *
     * @param  request  $request
     * @return object
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required',
            'phone'         => 'required|regex:@\+92\d{10}@|unique:users,phone',
            'code'          => 'required|regex:@\d{6}@',
            'merchant_id'   => 'required|regex:@\+92\d{10}@',
            'provider'      => 'required',
        ]);

        

        $user = User::create([
            'phone'         => $request->phone,
            'password'      => bcrypt($request->code),
            'provider_id'   => $request->provider_id,
            'provider'      => $request->provider,
            'active'        => 1, 
        ]);

        $profile = Profile::create([
            'user_id'           => $user->user_id,
            'phone_verified'    => 1,
            'added_by'          => $reqeust->provider_id,
        ]);

        $user = User::create($request->all());
        return response()->json($user);
    }

    /**
     * Update an existing member (User)
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function update(Request $request, $id)
    {
        $user = User::members()->find($id);

        if ($user) 
        {
            $user->update($request->all());
            return response()->json($user);
        }

        return response(array('message'=>'Not Found'), 404);
    }
}
