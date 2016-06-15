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

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|exists:users,email|max:100',
            'password' => 'required',
        ]);

        try {

            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) 
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

            return response()->json(['message' => 'Session terminated'], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['message' => 'Expired token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['message' => 'Invalid token'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['message' => $e->getMessage()], 500);

        }
    }

    public function postPwdHash(Request $request)
    {
        $this->validate($request, [
            'password' => 'required',
        ]);

        return app('hash')->make($request->password);
    }
}