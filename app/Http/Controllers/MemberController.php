<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Skill;
use Illuminate\Http\Request;

class MemberController extends Controller
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
        if (Auth::user()->can('list_all_members', Auth::user()))
        {
            if ($request->has('page'))
            {
                $users = User::members()->orderBy('user_id', 'desc')->skip($request->page*$this->limit)->take($this->limit)->get();
            }
            else
            {
                $users = User::members()->orderBy('user_id', 'desc')->take($this->limit)->get();
            }

            if (count($users)) return response()->json($users);

            return response()->json(['message'=>'Not Found'], 404);
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
        if (Auth::user()->can('view_member', Auth::user()))
        {
            $user = User::members()->find($id);

            if ($user) return response()->json($user);

            return response()->json(['message'=>'Not Found'], 404);
        }
        return response()->json(['message'=>'Forbidden'], 403);
    }

    /**
     * Create a new member
     *
     * @param  request  $request
     * @return object
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required|string|min:5|max:100',
            'phone'             => 'required|regex:@\+\d{8,15}@|unique:users,phone',
            'merchant_phone'    => 'required|regex:@\+\d{8,15}@',
        ]);

        $merchant = User::members()->where('phone', $request->merchant_phone)->first(); 

        if ($merchant && $merchant->user_id === Auth::user()->user_id)
        {
            try
            {
                DB::beginTransaction();

                $auth_code = mt_rand(100000, 999999);

                $user = User::create([
                    'name'          => $request->name,
                    'phone'         => $request->phone,
                    'password'      => app('hash')->make($auth_code),
                    'provider'      => 'Phone',
                    'confirm_code'  => $auth_code, 
                ]);

                $profile = Profile::create([
                    'user_id'           => $user->user_id,
                    'phone_verified'    => 1,
                    'added_by'          => $merchant->user_id,
                ]);

                DB::commit();

                return response()->json($user);
            }
            catch (Exception $e)
            {
                return $e; 
            }
        }
        else
        {
            return response()->json(['message'=>'Not Found'], 404);
        }
    }

    /**
     * Verify a newly created member (User)
     *
     * @param  request  $request
     * @return object
     */
    public function verify(Request $request, $id)
    {
        $this->validate($request, [
            'auth_code' => 'required|regex:@\d{6}@'
        ]); 

        $user = User::members()->where('user_id', $id)->where('confirm_code', $request->auth_code)->first();

        if ($user)
        {
            $user->active = 1;
            $user->confirm_code = null;
            $user->save();

            return response()->json($user); 
        }

        return response()->json(['message'=>'Not Found'], 404);
    }

    /**
     * Request authorization code for a specific user
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function edit(Request $request, $id)
    {
        $user = User::members()->find($id);

        if ($user) 
        {
            $auth_code = mt_rand(100000, 999999);

            $user->confirm_code = $auth_code; 
            $user->save();

            $message = 'SkillBazaar received an edit request for your account. Please use the following authorization code: '. $auth_code; 

            if (\App\Libraries\Common::sendSMS($user->phone, $message))
            {
                return response(array('message'=>'Authorization code sent to '.$user->phone), 200);
            }
            
            return response(array('message'=>'Could not send message. Try again.'), 500);
        }

        return response(array('message'=>'Not Found'), 404);
    }

    /**
     * Update an existing member (User)
     *
     * @param  request  $request
     * @param  integer  $id
     * @return object
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'phone'             => 'regex:@\+\d{8,15}@|unique:users,phone',
            'email'             => 'email|unique:users,email,'.$id.',user_id',
            'auth_code'         => 'required|regex:@\d{6}@|exists:users,confirm_code',
            'merchant_phone'    => 'required|regex:@\+\d{8,15}@',
        ]);
           
        $user = User::members()->where('confirm_code', $request->auth_code)->find($id);

        if ($user) 
        {
            $user->update($request->all());
            $user->confirm_code = null;
            $user->save();
            
            return response()->json($user);
        }

        return response(array('message'=>'Not Found'), 404);
    }
    /**
     * Get a single member's skills
     * 
     * @param   integer     id
     * @return  array
     */
    public function getSkills(Request $request, int $id)
    {
        if (Auth::user()->can('get_skills', Auth::user()))
        {
            $user = User::with('skills')->members()->find($id);

            if ($user)
            {
                $skills = array();
                foreach ($user->skills as $skill)
                {
                    array_push($skills, array('skill_id' => $skill->skill_id, 'skill_name' => $skill->skill_name));
                }
                return response()->json($skills, 200);
            }
            return response(array('message'=>'Not Found'), 404);
        }
        return response(array('message'=>'Forbidden'), 403);
    }
    /**
     * Assign new skill to a member
     * 
     * @param   integer     id
     * @param   integer     skill
     * @return  array
     */
    public function putSkill(Request $request, int $id)
    {
        if (Auth::user()->can('set_skills', Auth::user()))
        {
            //
            // Validate the request; make sure skill exists in the system
            //
            $this->validate($request, [
                'skill_id' => 'required|integer|exists:skills,skill_id',
            ]);
            //
            // Fetch the required user along with skills
            //
            $user   = User::with('skills')->members()->find($id);
            //
            // Return existing user object if skill already exists
            // 
            if ($user->skills()->where('skills.skill_id', $request->skill_id)->count())
            {
                return response()->json($user->skills, 200);
            }
            //
            // Fetch the new skill
            //
            $skill  = Skill::find($request->skill_id);
            //
            // Attach if both user and skill exist
            // 
            if ($user && $skill)
            {
                $user->skills()->attach($skill);
                $user->load('skills');

                return response()->json($user->skills, 200);
            }
            return response(array('message'=>'Not Found'), 404);
        }
        return response(array('message'=>'Forbidden'), 403);
    }
    /**
     * Unassign a skill from a member
     * 
     * @param   integer     id
     * @param   integer     skill
     * @return  array
     */
    public function delSkill(Request $request, int $id)
    {
        if (Auth::user()->can('del_skills', Auth::user()))
        {
            $this->validate($request, [
                'skill_id' => 'required|integer|exists:skills,skill_id',
            ]);

            $user   = User::with('skills')->members()->find($id);
            $skill  = Skill::find($request->skill_id);

            if ($user && $skill)
            {
                $user->skills()->detach($skill);
                $user->load('skills');

                return response()->json($user->skills, 200);
            }
            return response(array('message'=>'Not Found'), 404);
        }
        return response(array('message'=>'Forbidden'), 403);
    }
}
