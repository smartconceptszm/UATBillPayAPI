<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;

use Closure;

class RequestLoggerMiddleware
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
        if($txParams){
            if($txParams['error']==''){
                Log::info('('.$txParams['clientCode'].') '.
                            'Request Cycle completed: Session id: '.
                                $txParams['sessionId'].' - Phone: '.
                                $txParams['mobileNumber'].' - Journey: '.
                                $txParams['customerJourney']
                            );
            }else{
                Log::error('('.$txParams['clientCode'].') '.
                                $txParams['error'].'. - Session id: '.
                                $txParams['sessionId'].' - Phone: '.
                                $txParams['mobileNumber'].' - Journey: '.
                                $txParams['customerJourney']
                            );
            }
        }

    }

}
