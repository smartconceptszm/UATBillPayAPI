<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Closure;

class WebRequestLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
      
      //Log the Transaction Details
      $txParams = $request->ussdParams;
      if(!$txParams){
         $userName = 'Web Request:';
         if($user= Auth::user()){
            $userName = $user->username.":";
         }
         Log::channel('web_requests')->info($userName, [
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'payload' => $request->except(['password', 'token']),
                        'responseStatus' => $response->getStatusCode()
                  ]);
      }

    }

}
