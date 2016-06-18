<?php

namespace App\Http\Controllers;

use Auth;
use App\Log;
use App\User;
use Illuminate\Http\Request;
// use Jenssegers\Agent\Agent;

class LogController extends Controller
{
    private $limit = 10;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Return all logs, limited by pages
     *
     * @param  request  $request
     * @param  string   $page
     * @return object
     */
    public function index(Request $request)
    {
        // $agent = new Agent();
        // return ['browser' => $agent->browser(), 'platform' => $agent->platform(), 'device'=>$agent->device()];
        if (Auth::user()->can('view_all_logs', new User)) 
        {
            if ($request->has('page')) Log::skip($request->page*$this->limit)->take($this->limit)->orderBy('created_at', 'desc')->get();
            return Log::take($this->limit)->orderBy('created_at', 'desc')->get();
        }
        return response()->json(['message' => 'Forbidden'], 403);
    }

    public function getUserLog(Request $request, int $id)
    {
        $user = User::find($id);

        if ($user)
        {
            if (Auth::user()->can('view_all_logs', $user)) 
            {
                return Log::users($id)->limit(10)->orderBy('created_at', 'desc')->get();
            }

            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(['message' => 'Not Found'], 404);
    }
}
