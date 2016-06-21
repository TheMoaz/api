<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * Process incoming login request
     * @param  string  $identity (email or phone)
     * @param  string  $password 
     * @return object
     */
    public function login(Request $request)
    {
        // 
        // Check if entered identity is email or phone
        //
        $field = filter_var($request->input('identity'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        //
        // Prepare the validators
        //
        if ($field == 'email')
        {
            $this->validate($request, [
                'identity' => 'required|email|exists:users,email|min:5|max:100',
                'password' => 'required|string|min:5|max:100',
            ]);
        }
        else
        {
            $this->validate($request, [
                'identity' => 'required|regex:@\+\d{8,15}@|exists:users,phone|min:5|max:100',
                'password' => 'required|string|min:5|max:100',
            ]);
        }

        try {

            $attempt = array(
                $field => $request->input('identity'), 
                'password' => $request->input('password')
            );

            if (! $token = $this->jwt->attempt($attempt)) 
            {
                return response()->json(['message' => 'Bad Request'], 400);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['message' => 'Expired token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['message' => 'Invalid token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['message' => $e->getMessage()], 500);

        }

        return response()->json(compact('token'));
    }

    /**
     * Generates hashed password of input
     *
     * @param  string  $password 
     * @return object
     */
    public function pwdhash(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
        ]);

        return app('hash')->make($request->password);
    }

    /**
     * Returns the authenticated user object
     * 
     * @return object
     */
    public function show(Request $request)
    {
        return response()->json(\Auth::user());
    }

    /**
     * Generates a new password and sends confirm_code to registered identity
     *
     * @return object
     */
    public function reset(Request $request)
    {
        $user = Auth::user();

        $auth_code = mt_rand(100000, 999999);

        if ($user->phone)
        {
            $message = 'SkillBazaar authorization code: ' . $auth_code; 

            if (\App\Libraries\Common::sendSMS($user->phone, $message))
            {
                $user->confirm_code = $auth_code;
                $user->password = app('hash')->make($auth_code);
                $user->save();

                return response()->json(['message' => 'Password has been reset'], 200);
            }
            return response()->json(['message' => 'Message Not Sent. Try again.'], 500);
        }
        else
        {
            if (\App\Libraries\Common::sendMail($user, 'Your SkillBazaar password has been reset', $auth_code, 'forgot'))
            {
                $user->confirm_code = $auth_code;
                $user->password = app('hash')->make($auth_code);
                $user->save();

                return response()->json(['message' => 'Password has been reset'], 200);
            }
            return response()->json(['message' => 'Message Not Sent. Try again.'], 500);
        }
    }

    /**
     * De-authorizes the supplied token
     *
     * @return object
     */
    public function logout(Request $request)
    {
        try
        {
            Auth::logout();

            return response()->json(['message' => 'Token unauthorized'], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['message' => 'Expired token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['message' => 'Invalid token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['message' => $e->getMessage()], 500);

        }
    }


    
}