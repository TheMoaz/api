<?php

namespace App\Http\Controllers;

use Auth;
use App\Log;
use App\User;
use Illuminate\Http\Request;

class ProviderController extends Controller
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
     * List all providers
     *
     * @return object
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('list_providers', Auth::user()))
        {
            if ($request->has('page') && is_numeric($request->page))
            {
                $users = User::providers()->skip($request->page*$this->limit)->take($this->limit)->get();
            }
            else
            {
                $users = User::providers()->take($this->limit)->get();
            }

            if (count($users)) return response($users);

            return response(array('message'=>'Not Found'), 404);
        }
        return response()->json(['message'=>'Forbidden'], 403);
    }

    /**
     * Return User object with specific user_id
     *
     * @param  int  $id
     * @return object
     */
    public function show($id)
    {
        $provider = User::providers()->find($id);

        if ($provider)
        {
            if (Auth::user()->can('show_provider', $provider))
            {
                return response()->json($provider);
            }

            return response()->json(['message'=>'Forbidden'], 403);
        }

        return response(array('message'=>'Not Found'), 404);
    }

    /**
     * Show activity log of provider merchants
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function activity(Request $request, $id)
    {
        $provider = User::providers()->find($id);

        if ($provider)
        {
            if (Auth::user()->can('view_provider_log', $provider)) 
            {
                if ($request->has('page') && is_numeric($request->page))
                {
                    return Log::providers($id)->orderBy('created_at', 'desc')->skip($request->page*$this->limit)->take($this->limit)->get();
                }
                return Log::providers($id)->orderBy('created_at', 'desc')->take($this->limit)->get();
            }
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return response()->json(['message' => 'Not Found'], 404);
    }
}
