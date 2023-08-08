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
      $smsClientKey = '';
      if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
         $smsClientKey = 'MockDeliverySMS';
      }
      if(!$smsClientKey &&  \array_key_exists('urlPrefix',$params)){
         if(\config('efectivo_clients.'.$params['urlPrefix'].'.hasOwnSMS')){
            $smsClientKey = \strtoupper($params['urlPrefix']).'SMS';
         }
      }
      if(!$smsClientKey){
         $smsClientKey = \env('SMPP_CHANNEL');
      }
      $this->smsClientBinder->bind($smsClientKey);
      return $next($request);
      
   }
   
}
