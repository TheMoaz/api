<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use App\Activity;
use App\Log;
use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggingMiddleware
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
        //

        return $next($request);
    }

    /**
     * Handle a terminating request. 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function terminate($request, $response)
    {
        //
        // Create a log entry in the database
        //
        $dlog = Log::create([
            'log_ip'        => $request->ip(),
            'log_user'      => \Auth::check() ? \Auth::user()->user_id : null,
            'log_method'    => $request->method(),
            'log_path'      => $request->path() . str_replace($request->url(), '', $request->fullUrl()),
            'log_status'    => $response->status(),
        ]);
        //
        // Separately log details in the activity log file
        //
        $log = new Logger('API');
        $log->pushHandler(new StreamHandler(storage_path('/logs/activity/'.date('Y-m-d').'.log'), Logger::INFO));
        $log->addinfo($dlog->id, array(
            'request'   => $request, 
            'input'     => null, //json_encode($request->input()), keeping it off in case photos/videos
            'response'  => $response,
        ));
    }
}
