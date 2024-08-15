<?php

namespace App\Jobs;

use App\Jobs\Middleware\SMSClientBindJobMiddleware;
use App\Http\Services\Web\SMS\SMSService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\SMSTxDTO;
use App\Jobs\BaseJob;

class SendSMSesJob extends BaseJob
{

   public $timeout = 600;
   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(
      private Array $arrSMSes, 
      public String $client_id = '')
   {}


   /**
    * Get the middleware the job should pass through.
    *
    * @return array<int, object>
    */
   public function middleware(): array
   {
      return [new SMSClientBindJobMiddleware(new \App\Http\Services\Web\Clients\ClientMnoService(new \App\Models\ClientMno()))];
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(SMSService $smsService, SMSTxDTO $smsTxDTO)
   {

      foreach ($this->arrSMSes as $smsData) {
         $smsTxDTO = $smsService->send($smsTxDTO->fromArray($smsData));
         if($smsData['type'] != "RECEIPT"){
            Log::info('('.$smsTxDTO->urlPrefix.') '.
                        'SMS Message Dispatched: Phone: '.
                        $smsData['mobileNumber'].' - :Type '.
                        $smsData['type'].' - Message: '.
                        $smsData['message']
                     );
         }
      }

   }

}
