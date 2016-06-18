<?php

namespace App\Http\Controllers;

use Auth;
use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MerchantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
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
            $users = User::merchants()->skip($request->page*10)->take(10)->get();
        }
        else
        {
            $users = User::merchants()->take(10)->get();
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
        $user = User::merchants()->find($id);

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
            'name'      => 'required|min:5|max:100', 
            'phone'     => 'required|unique:users,phone', 
            'provider'  => 'required',
        ]);

        $auth_code = mt_rand(100000, 999999);

        $user = User::create([
            'name'          => $request->name,
            'phone'         => $request->phone,
            'provider'      => $request->provider,
            'role'          => 'Merchant',
            'confirm_code'  => $auth_code, 
            'password'      => \Illuminate\Support\Facades\Crypt::encrypt($auth_code),
        ]);

        //
        // SEND SMS TO PHONE NUMBER (To be implemented)
        //

        return response()->json($user);
    }

    /**
     * Verify a newly created merchant (User)
     *
     * @param  request  $request
     * @return object
     */
    public function verify(Request $request)
    {
        $this->validate($request, [
            'id'        => 'required|integer|exists:users,user_id,active,0',
            'code'      => 'required|regex:@\d{6}@', 
        ]); 

        $user = User::merchants()->where('user_id', $request->id)->where('confirm_code', $request->code)->first();

        if ($user)
        {
            $user->active = 1;
            $user->confirm_code = null;
            $user->save();
            return response()->json($user); 
        }

        return response(array('message'=>'Not Found'), 404);
    }

    /**
     * Update an existing merchant (User)
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function update(Request $request, $id)
    {
        $user = User::merchants()->find($id);

        if ($user) 
        {
            $user->update($request->all());
            return response()->json($user);
        }

        return response(array('message'=>'Not Found'), 404);
    }

    /**
     * Show activity log of merchant
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function log(Request $request, int $id)
    {
        $user = User::merchants()->find($id);

        if ($user)
        {
            if (Auth::user()->can('view_merchant_logs', $user)) 
            {
                return Log::merchants($id)->limit(10)->orderBy('created_at', 'desc')->get();
            }

            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }
}
