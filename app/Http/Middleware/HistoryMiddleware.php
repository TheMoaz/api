<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use App\Activity;
use App\History;
use Illuminate\Http\Request;

class HistoryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // return $request;
        // 
        // Default logging of all activities
        //
        History::create([
            'log_ip'        => $request->ip(),
            'log_user'      => \Auth::check() ? \Auth::user()->user_id : null,
            'log_method'    => $request->method(),
            'log_path'      => $request->path(),
            'log_agent'     => $request->header('user-agent'),
            // 'log_activity'  => null,
            'log_input'     => count($request->input()) ? json_encode($request->input()) : null,
            //'log_header'    => json_encode($request->header()),
        ]);

        return $next($request);
    }
}
