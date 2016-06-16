<?php

namespace App\Http\Controllers;

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

    public function postRegister(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email|max:100'
        ]);
    }

    public function postLogin(Request $request)
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
                return response()->json(['message' => 'Not Found'], 404);
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

    public function postLogout(Request $request)
    {
        try
        {
            \Auth::logout();

            return response()->json(['message' => 'Token unauthorized'], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['message' => 'Expired token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['message' => 'Invalid token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    public function postPasswordHash(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
        ]);

        return app('hash')->make($request->password);
    }

    public function postResetPassword(Request $request)
    {

    }

    public function getAuthenticatedUser(Request $request)
    {
        return \Auth::user();
    }
}