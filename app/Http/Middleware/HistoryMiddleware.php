<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use App\Activity;
use App\History;

class HistoryMiddleware
{
    /**
     * This serves as one place to put all activities for reference
     *  1.  "signed up"
     *  2.  "signed in"
     *  3.  "searched"
     *  4.  "created"
     *  5.  "edited"
     *  6.  "viewed"
     *  7.  "reviewed"
     *  8.  "contacted"
     *  9.  "reported"
     *  10. "voted up"
     *  11. "voted down"
     *  12. "recommended"
     */

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('v1/user'))
        {
            History::create([
                'user_id'       => \Auth::user()->user_id,
                'activity_id'   => 8,
                'target_id'     => $request->id
            ]);
        }

        if ($request->is('profile/*/review'))
        {
            if (\Auth::check())
            {
                History::create([
                    'user_id'       => \Auth::user()->user_id,
                    'activity_id'   => 7,
                    'target_id'     => $request->id
                ]);
            }
        }

        if ($request->is('profile/*'))
        {
            DB::table('profiles')->where('user_id', $request->id)->increment('views');

            if (\Auth::check())
            {
                $user = \App\User::find($request->id);

                History::create([
                    'user_id'       => \Auth::user()->user_id,
                    'activity_id'   => 6,
                    'target_id'     => $request->id
                ]);
            }
        }
        
        if ($request->is('skill/*'))
        {
            if (\Auth::check())
            {
                $skill = \App\Skill::find($request->id);
                                
                History::create([
                    'user_id'       => \Auth::user()->user_id,
                    'activity_id'   => $request->vote == 'yea' ? 10 : 11,
                    'target_id'     => $request->id
                ]);
            }
        }

        if ($request->is('search'))
        {
            if (\Auth::check())
            {
                $skill = \App\Skill::where('skill_name', $request->q)->first();

                History::create([
                    'user_id'       => \Auth::user()->user_id,
                    'activity_id'   => 3,
                    'target_id'     => count($skill) > 0 ? $skill->skill_id : 0
                ]);
            }
        }

        return $next($request);
    }
}
