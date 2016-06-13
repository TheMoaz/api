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
            $users = User::members()->orderBy('user_id', 'desc')->skip($request->page*10)->take(10)->get();
        }
        else
        {
            $users = User::members()->orderBy('user_id', 'desc')->take(10)->get();
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
     * Request authorization code for a specific user
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function edit(Request $request, $id)
    {
        $user = User::members()->find($id);

        if ($user) 
        {
            $auth_code = mt_rand(100000, 999999);

            $user->confirm_code = $auth_code; 
            $user->save();

            $message = 'SkillBazaar received an edit request for your account. Please use the following authorization code: '. $auth_code; 

            if (\App\Libraries\Common::sendSMS($user->phone, $message))
            {
                return response(array('message'=>'Authorization code sent to '.$user->phone), 200);
            }
            
            return response(array('message'=>'Could not send message. Try again.'), 500);
        }

        return response(array('message'=>'Not Found'), 404);
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
        $this->validate($request, [
            'phone'             => 'regex:@\+\d{8,15}@|unique:users,phone',
            'email'             => 'email|unique:users,email',
            'auth_code'         => 'required|regex:@\d{6}@',
            'merchant_phone'    => 'required|regex:@\+\d{8,15}@',
        ]);
           
        $user = User::members()->where('confirm_code', $request->auth_code)->find($id);

        if ($user) 
        {
            $user->update($request->all());
            $user->confirm_code = null;
            $user->save();
            
            return response()->json($user);
        }

        return response(array('message'=>'Not Found'), 404);
    }
}
