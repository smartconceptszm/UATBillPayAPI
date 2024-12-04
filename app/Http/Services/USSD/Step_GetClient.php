<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Sessions\SessionService;
use App\Http\Services\Clients\ClientService; 
use App\Http\DTOs\BaseDTO;

class Step_GetClient extends EfectivoPipelineContract
{
    
   public function __construct(
      private SessionService $sessionService,
      private ClientService $clientService)
   {}
   
   protected function stepProcess(BaseDTO $txDTO)
   {

      try {
         
         $client = $this->clientService->findOneBy(['urlPrefix'=>$txDTO->urlPrefix]);
         $txDTO->ussdAggregator = $client->ussdAggregator; 
         $txDTO->clientSurcharge = $client->surcharge; 
         $txDTO->testMSISDN = $client->testMSISDN;
         $txDTO->shortCode = $client->shortCode;
         $txDTO->urlPrefix = $client->urlPrefix;
         $txDTO->client_id = $client->id;
         
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);

         if($client->mode != 'UP'){
            $txDTO->error = 'System in Maintenance Mode';
            $txDTO->response = $billpaySettings['MODE_MESSAGE'];
            $txDTO->exitPipeline = true;
            $txDTO->lastResponse = true;
         }

         if($client->status != 'ACTIVE'){
            $txDTO->response=$billpaySettings['BLOCKED_MESSAGE']." ".\strtoupper($txDTO->urlPrefix);
            $txDTO->error = 'Client is blocked';
            $txDTO->lastResponse = true;
            $txDTO->exitPipeline = true;
         }

      } catch (\Throwable $e) {
         $txDTO->error = 'At identify client step. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
         $txDTO->handler = 'DummyMenu';
      }
      return $txDTO;
      
   }

}

