<?php

namespace App\Http\Services\USSD\Sessions\ExistingSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Clients\ClientService;
use App\Http\DTOs\BaseDTO;

class RetrieveSession extends EfectivoPipelineContract
{

   public function __construct(
      private SessionService $sessionService,
      private ClientService $clientService)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         $ussdSession = $this->sessionService->findOneBy([   
                                                         'mobileNumber'=>$txDTO->mobileNumber,
                                                         'sessionId'=>$txDTO->sessionId,
                                                      ]);
         if($txDTO->ussdAggregator == 'YES'){
            $client = $this->clientService->findById($ussdSession->client_id);
            $txDTO->ussdAggregator = $client->ussdAggregator; 
            $txDTO->clientSurcharge = $client->surcharge; 
            $txDTO->testMSISDN = $client->testMSISDN;
            $txDTO->shortCode = $client->shortCode;
            $txDTO->urlPrefix = $client->urlPrefix;
            $txDTO->client_id = $client->id;
         }

         $txDTO->customerJourney = $ussdSession->customerJourney;
         $txDTO->customerAccount = $ussdSession->customerAccount;
         $txDTO->paymentAmount = $ussdSession->paymentAmount;
         $txDTO->revenuePoint = $ussdSession->revenuePoint;
         $txDTO->created_at = $ussdSession->created_at;
         $txDTO->menu_id = $ussdSession->menu_id;
         $txDTO->mno_id = $ussdSession->mno_id;
         $txDTO->status = $ussdSession->status;
         $txDTO->id = $ussdSession->id;
         $txDTO->error = '';

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}