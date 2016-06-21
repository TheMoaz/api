<?php

namespace App\Http\Controllers;

use Auth;
use App\Log;
use App\User;
use App\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MerchantController extends Controller
{
    private $limit;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->limit = env('PER_PAGE', 10);
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
        if (Auth::user()->can('add_merchant', Auth::user()))
        {
            $this->validate($request, [
                'name'      => 'required|string|min:5|max:100', 
                'phone'     => 'required|unique:users,phone'
            ]);

            //
            // Generate 6-digit code; used as password and confirm_code
            //
            $auth_code = mt_rand(100000, 999999);

            try
            {
                $user = User::create([
                    'name'          => $request->name,
                    'phone'         => \App\Libraries\Common::format_phone($request->phone), 
                    'provider'      => Auth::user()->provider,
                    'role'          => 'Merchant',
                    'active'        => 1, 
                    'confirm_code'  => $auth_code, 
                    'password'      => app('hash')->make($auth_code),
                ]);

                //
                // Send SMS to phone number
                //
                $message = 'Welcome to SkillBazaar. Please use the following code to login to your account: ' . $auth_code; 

                if (\App\Libraries\Common::sendSMS($user->phone, $message))
                {
                    return response()->json($user, 201);
                }
                return response()->json(['message' => 'Error sending SMS.'], 500);
            }
            catch (\Illuminate\Database\QueryException $e)
            {
                return response()->json(['message' => 'The phone has already been taken.'], 422);
            }
        }
        return response()->json(['message' => 'Forbidden'], 403);
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
    public function activity(Request $request, $id)
    {
        $user = User::merchants()->find($id);

        if ($user)
        {
            if (Auth::user()->can('view_merchant_logs', $user)) 
            {
                if ($request->has('page')) 
                {
                    return Log::merchants($id)->skip($request->page*$this->limit)->take($this->limit)->orderBy('created_at', 'desc')->get();
                }
                return Log::merchants($id)->take($this->limit)->orderBy('created_at', 'desc')->get();
            }

            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }
}
