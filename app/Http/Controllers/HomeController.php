<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $functions = array(
            'Public' => [
                'Login'           => '/login',
                'Logout'          => '/logout',
                'Current user'    => '/user',
            ],
            'Private' => [
                'List all members' => '/v1/users',
                'Show member profile' => '/v1/users/{id}',
                'Add new member' => '/v1/users/add',
                'Verify new member' => '/v1/users/{id}/verify',
                'Request member edit authorization' => '/v1/users/{id}/edit',
                'Submit member edit request' => '/v1/users/{id}/edit',
            ]
        );

        return response()->json($functions);
    }

    public function upload(Request $request)
    {
        $this->validate($request, [
            'photo' => 'required_without:video|mimetypes:image/jpeg,image/gif,image/png',
            'video' => 'required_without:photo|mimetypes:video/mpeg,video/quicktime,video/mp4,video/webm,video/x-ms-wmv',
        ]);

        $response = \App\Libraries\Common::upload($request);

        if ($response)
        {
            return response()->json($response, 201);
        }
        
        return response()->json(['message' => ''], 422);
    }
}
