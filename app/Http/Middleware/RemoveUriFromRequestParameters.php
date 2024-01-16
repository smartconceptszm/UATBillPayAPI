<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
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

        $requestUrlArr = \explode("/",$request->url());
        $uri = $requestUrlArr[\count($requestUrlArr)-1];
        $requestParameters = $request->all();
        if(\key_exists($uri,$requestParameters) && substr($uri,0,1) == "/"){
            unset($request[$uri]);
            Log::info('(EFECTIVO params at middleware:) '.\json_encode($request->query()));
        }
        return $next($request);
        
    }
}
