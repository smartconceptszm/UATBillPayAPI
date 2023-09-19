<?php

namespace App\Jobs\Middleware;

use App\Http\Services\External\SMSClients\SMSClientBinderService;
use Closure;

class SMSClientBindJobMiddleware
{

   public function __construct(
      private SMSClientBinderService $smsClientBinder) 
   {}

    /**
     * Process the queued job.
     *
     * @param  \Closure(object): void  $next
     */

   public function handle(object $job, Closure $next): void
   {

      $smsClientKey = '';
      if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
         $smsClientKey = 'MockDeliverySMS';
      }
      if(!$smsClientKey &&  isset($job->urlPrefix)){
         if(\config('efectivo_clients.'.$job->urlPrefix.'.hasOwnSMS')){
            $smsClientKey = \strtoupper($job->urlPrefix).'SMS';
         }
      }
      if(!$smsClientKey){
         $smsClientKey = \env('SMPP_CHANNEL');
      }
      $this->smsClientBinder->bind($smsClientKey);
      $next($job);
      
   }
   
}
