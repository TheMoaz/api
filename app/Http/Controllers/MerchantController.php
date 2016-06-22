<?php

namespace App\Http\Controllers;

use Auth;
use App\Log;
use App\User;
use App\Common;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    //
    // Define per page limit
    //
    private $limit;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->limit = env('PER_PAGE', 10);
    }

    /**
     * List all merchants 
     *
     * @return object
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('list_own_merchants', Auth::user()))
        {
            if ($request->has('page') && is_numeric($request->page))
            {
                $users = User::merchants(Auth::user())->orderBy('created_at', 'desc')->skip($request->page*$this->limit)->take($this->limit)->get();
            }
            else
            {
                $users = User::merchants(Auth::user())->orderBy('created_at', 'desc')->take($this->limit)->get();
            }

            if (count($users)) 
            {
                return response()->json($users);
            }
            return response(array('message'=>'Not Found'), 404);
        }
        return response(array('message'=>'Forbidden'), 403);
    }

    /**
     * Return User object with specific user_id
     *
     * @param  int  $id
     * @return object
     */
    public function show($id)
    {
        if (Auth::user()->can('view_own_merchant', Auth::user()))
        {
            $user = User::merchants(Auth::user())->find($id);

            if (count($user)) 
            {
                return response()->json($user);
            }

            return response(array('message'=>'Not Found'), 404);
        }
        return response(array('message'=>'Forbidden'), 403);
    }

    /**
     * Create a new merchant (User)
     *
     * @param  request  $request
     * @return object
     */
    public function create(Request $request)
    {
        if (Auth::user()->can('add_new_merchant', Auth::user()))
        {
            $this->validate($request, [
                'name'      => 'required|string|min:5|max:100', 
                'phone'     => 'required|regex:@\+\d{8,15}@|unique:users,phone'
            ]);

            //
            // Generate 6-digit code; used as password and confirm_code
            //
            $auth_code = mt_rand(100000, 999999);

            try
            {
                $merchant = User::create([
                    'name'          => $request->name,
                    'phone'         => \App\Libraries\Common::format_phone($request->phone), 
                    'provider'      => Auth::user()->name,
                    'role'          => 'Merchant',
                    'active'        => 1, 
                    'confirm_code'  => $auth_code, 
                    'password'      => app('hash')->make($auth_code),
                ]);
                //
                // Send SMS to phone number
                //
                $message = 'Welcome to SkillBazaar. Please use the following code to login to your account: ' . $auth_code; 

                if (\App\Libraries\Common::sendSMS($merchant->phone, $message))
                {
                    return response()->json($merchant, 201);
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
    // public function verify(Request $request)
    // {
    //     $this->validate($request, [
    //         'id'        => 'required|integer|exists:users,user_id,active,0',
    //         'code'      => 'required|regex:@\d{6}@', 
    //     ]); 

    //     $user = User::merchants()->where('user_id', $request->id)->where('confirm_code', $request->code)->first();

    //     if ($user)
    //     {
    //         $user->active = 1;
    //         $user->confirm_code = null;
    //         $user->save();
    //         return response()->json($user); 
    //     }

    //     return response(array('message'=>'Not Found'), 404);
    // }

    /**
     * Update an existing merchant
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function update(Request $request, $id)
    {
        $merchant = User::merchants(Auth::user())->find($id);

        if ($merchant)
        {
            if (Auth::user()->can('edit_own_merchant', $merchant))
            {
                $merchant->update($request->all());
                return response()->json($merchant);
            }
            return response(array('message'=>'Forbidden'), 403);
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
        $merchant = User::merchants(Auth::user())->find($id);

        if ($merchant)
        {
            if (Auth::user()->can('view_own_merchant_log', $merchant)) 
            {
                if ($request->has('page') && is_numeric($request->page)) 
                {
                    return Log::merchants($id)->orderBy('created_at', 'desc')->skip($request->page*$this->limit)->take($this->limit)->get();
                }
                return Log::merchants($id)->orderBy('created_at', 'desc')->take($this->limit)->get();
            }
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json(['message' => 'Not Found'], 404);
    }
}
