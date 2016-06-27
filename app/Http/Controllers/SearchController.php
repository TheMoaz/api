<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Skill;
use Illuminate\Http\Request;

class SearchController extends Controller
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
        $this->limit = env('PER_PAGE', 10);
    }

    /**
     * List all users - restricted by $limit
     *
     * @return object
     */
    public function index(Request $request)
    {
        // 
        // Deny if user cannot search
        //
        if (Auth::check() && Auth::user()->cannot('search_members', Auth::user())) 
            return response()->json(['message'=>'Forbidden'], 403);
        //
        // Deny if there is no search term
        // 
        if (!$request->has('q') || is_null($request->q)) 
            return response()->json(['message'=>'Bad Request'], 400);
        //
        // Start building search query; searching only for members
        //
        $users = User::members();
        //
        // Join with profiles and locations
        // 
        $users = $users->join('profiles', 'profiles.user_id', '=', 'users.user_id')
                       ->join('locations', function($join) 
                         {
                            $join->on('locations.user_id', '=', 'users.user_id')->where('locations.type', '=', 'Present');
                         })
                       ->where('locations.latitude', '<>', 0)
                       ->where('locations.longitude', '<>', 0);
        //
        // Look for skills matching search term
        //
        $skill = Skill::where('skill_name', str_singular($request->q))->count() > 0 ? true : false;

        if ($skill)
        {
            $users = $users->join('skill_user', 'skill_user.user_id', '=', 'users.user_id')
                           ->join('skills', 'skills.skill_id', '=', 'skill_user.skill_id')
                           ->where('skills.skill_name', 'like', '%'.str_singular($request->q).'%');
        }
        else
        {
            $users = $users->where('users.name', 'like', '%'.$request->q.'%');
        }
        //
        // Only search for active profiles
        //
        $users = $users->where('profiles.active', 1); 
        //
        // Determine sorting
        //
        $users = $users->orderBy('profiles.rating', 'desc');
        $users = $users->orderBy('users.name', 'asc');
        //
        // Determine pagination
        // 
        if ($request->has('page'))
        {
            $users = $users->skip($request->page*$this->limit)->take($this->limit);
        }
        else
        {
            $users = $users->take($this->limit);
        }
        //
        // Fetch results
        //
        $users = $users->get();

        if (count($users)) return response()->json($users);

        return response()->json(['message'=>'Not Found'], 404);
    }
}
