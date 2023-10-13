<?php

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\App;
use Closure;

class SMSClientBindJobMiddleware
{

    /**
     * Process the queued job.
     *
     * @param  \Closure(object): void  $next
     */

   public function handle(object $job, Closure $next): void
   {

      $smsClientKey = '';
      if(!$smsClientKey && (\env('SMS_SEND_USE_MOCK') == "YES")){
         $smsClientKey = 'MockSMSDelivery';
      }
      if(!$smsClientKey &&  isset($job->urlPrefix)){
         if(\env(\strtoupper($job->urlPrefix).'_HAS_OWNSMS') == 'YES'){
            $smsClientKey = \strtoupper($job->urlPrefix).'SMS';
         }
      }
      if(!$smsClientKey){
         $smsClientKey = \env('SMPP_CHANNEL');
      }
      App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClientKey);
      $next($job);
      
   }
   
}
