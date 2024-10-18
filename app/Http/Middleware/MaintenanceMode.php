<?php

namespace App\Http\Middleware;

use Closure;

class MaintenanceMode
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

        $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
        if($billpaySettings['APP_MODE']==='UP'){
            return $next($request);
        }else{
            return response($billpaySettings['MODE_MESSAGE']);
        }
        
    }
}
