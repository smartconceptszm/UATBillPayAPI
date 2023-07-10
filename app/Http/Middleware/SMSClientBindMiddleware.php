<?php

namespace App\Http\Middleware;

use App\Http\BillPay\Services\External\SMSClients\SMSClientBinderService;
use Closure;

class SMSClientBindMiddleware
{

    private $smsClientBinder;
    public function __construct(SMSClientBinderService $smsClientBinder)
    {
        $this->smsClientBinder = $smsClientBinder;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {

        $params = $request->all();
        $smsClientKey = \env('SMPP_CHANNEL');
        if(\array_key_exists('urlPrefix',$params)){
            if(\config('efectivo_clients.'.$params['urlPrefix'].'.hasOwnSMS')){
                $smsClientKey = \strtoupper($params['urlPrefix']).'SMS';
            }
        }
        $this->smsClientBinder->bind($smsClientKey);
        return $next($request);
        
    }
}
