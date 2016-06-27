<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
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
     * List all users - restricted by $limit
     *
     * @return object
     */
    public function index(Request $request)
    {
        //
        // Start building the query; check authorization, select relevant filters
        //
        if (Auth::user()->can('list', Skill::class))
        {
            $skills = Skill::active()->used()->select('skills.skill_id', 'skills.skill_name');
            //
            // Setup paging (skip, take)
            //
            if ($request->has('page') && is_numeric($request->page)) 
            {
                $skills = $skills->skip($request->page*$this->limit)->take($this->limit);
            }
            //
            // Define the filter, and sort orders
            //
            $skills = $skills->havingRaw('count(*) > 1')->orderByRaw('count(*) desc')->orderBy('skill_name', 'asc');
            //
            // Finally, return the results (if any)
            // 
            if (count($skills)) 
            {
                return response()->json($skills->get());
            }
            return response()->json(['message'=>'Not Found'], 404);
        }
        else 
        {
            return response()->json(['message'=>'Forbidden'], 403);
        }
        
    }

    /**
     * Return User object with specific user_id
     *
     * @param  int  $id
     * @return object
     */
    public function show(Request $request, $id)
    {
        if (Auth::user()->can('show', Skill::class))
        {
            $skill = Skill::find($id);

            if ($skill)
            {
                return response()->json($skill);
            } 
            return response()->json(['message'=>'Not Found'], 404);
        }
        return response()->json(['message'=>'Forbidden'], 403);
    }

    /**
     * Create a new skill
     *
     * @param  request  $request
     * @return object
     */
    public function create(Request $request)
    {
        if (Auth::user()->can('create', Skill::class))
        {
            $this->validate($request, [
                'skill' => 'required|unique:skills,skill_name|min:5|max:100',
            ]);

            $skill = Skill::create([
                'skill_name' => strtolower($request->skill),
                'added_by'   => Auth::user()->user_id,
            ]);

            return response()->json($skill, 201);
        }
        return response()->json(['message'=>'Forbidden'], 403);
    }

    /**
     * Update an existing skill
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function update(Request $request, $id)
    {
        $skill = Skill::find($id);

        if ($skill)
        {
            if (Auth::user()->can('edit', $skill))
            {
                $this->validate($request, [
                    'skill'     => 'required|unique:skills,skill_name,'.$id.',skill_id|min:5|max:100',
                    'active'    => 'boolean',
                ]);

                $skill->skill_name  = strtolower($request->skill);
                $skill->active      = $request->has('active') ? (bool)$request->active : $skill->active;
                $skill->save();
                
                return response()->json($skill);
            }
            return response()->json(['message'=>'Forbidden'], 403);
        }
        return response(array('message'=>'Not Found'), 404);
    }
}
