<?php

namespace App\Jobs\Middleware;

use App\Http\Services\Web\Clients\ClientMnoService;
use Illuminate\Support\Facades\App;
use Closure;

class SMSClientBindJobMiddleware
{

   public function __construct(
		private ClientMnoService $clientMnoService)
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
         $smsClientKey = 'MockSMSDelivery';
      }

      if(!$smsClientKey){
         $clientMNOs = $this->clientMnoService->findOneBy([
                                             'client_id' => $job->client_id,
                                             'smsActive' => 'YES',
                                             'smsMode' => 'UP'
                                          ]);
         $smsClientKey = $clientMNOs->handler;
      }
      App::bind(\App\Http\Services\External\SMSClients\ISMSClient::class,$smsClientKey);
      $next($job);
      
   }
   
}
