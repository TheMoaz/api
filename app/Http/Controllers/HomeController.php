<?php

namespace App\Http\Controllers;

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

}
