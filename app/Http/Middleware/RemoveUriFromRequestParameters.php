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
        Log::info('(EFECTIVO middleware Request URI:) '.$uri);
        Log::info('(EFECTIVO middleware Params 1:) '.\json_encode($requestParameters));
        if(\key_exists($uri,$requestParameters) && substr($uri,0,1) == "/"){
            unset($request[$uri]);
            Log::info('(EFECTIVO params at middleware 2:) '.\json_encode($request->query()));
        }
        return $next($request);
        
    }
}
