<?php

namespace App\Jobs;

use App\Jobs\Middleware\SMSClientBindJobMiddleware;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\SMS\SMSService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\SMSTxDTO;
use App\Jobs\BaseJob;

class SendSMSesJob extends BaseJob
{

   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(
      private Array $arrSMSes, 
      public String $urlPrefix = '')
   {}


   /**
    * Get the middleware the job should pass through.
    *
    * @return array<int, object>
    */
   public function middleware(): array
   {
      return [new SMSClientBindJobMiddleware()];
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(SMSService $smsService, SMSTxDTO $smsTxDTO, ClientService $clientService )
   {

      foreach ($this->arrSMSes as $smsData) {
         $smsService->send($smsTxDTO->fromArray($smsData));
         if($smsData['type'] != "RECEIPT"){
            $client = $clientService->findById($smsData['id']);
            Log::info('('.$client->urlPrefix.') '.
                           'SMS Message Dispatched: Phone: '.
                              $smsData['mobileNumber'].' - :Type '.
                              $smsData['type'].' - Message: '.
                              $smsData['message']
                           );
         }
      }

   }

}
