<?php

namespace App\Http\Middleware;

use Closure;

class RemoveUriFromRequestParameters
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

        $theURIKey='/'.$request->path();
        $requestParameters = $request->all();
        if(\key_exists($theURIKey,$requestParameters)){
            unset($request[$theURIKey]);
        }
        return $next($request);
        
    }
}
