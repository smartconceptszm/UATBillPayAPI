<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Web\Clients\AggregatedClientService; 
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Web\Sessions\SessionService;
use App\Http\Services\Web\Clients\ClientService; 
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_IdentifyClient extends EfectivoPipelineContract
{
    
   public function __construct(
      private AggregatedClientService $aggregatedClientService,
      private SessionService $sessionService,
      private ClientService $clientService)
   {}
   
   protected function stepProcess(BaseDTO $txDTO)
   {

      if($txDTO->error == ''){ 
         try {
            
            $client = $this->clientService->findOneBy(['urlPrefix'=>$txDTO->urlPrefix]);
            
            if($client->ussdAggregator == 'YES'){
               if($txDTO->isNewRequest == '1'){
                  $subscriberInput = \explode("*",$txDTO->subscriberInput);
                  if(\count($subscriberInput)>1){
                     $aggregatedClient = $this->aggregatedClientService->findOneBy([
                                                      'parent_id'=>$client->id,
                                                      'menuNo'=>$subscriberInput[1]
                                                   ]);
                     $client = $this->clientService->findById($aggregatedClient->client_id);
                     $txDTO->subscriberInput = $subscriberInput[0];
                  }
               }else{
                  $ussdSession = $this->sessionService->findOneBy([   
                                                            'mobileNumber'=>$txDTO->mobileNumber,
                                                            'sessionId'=>$txDTO->sessionId,
                                                         ]);
                  if($ussdSession->client_id != $client->id){
                     $client = $this->clientService->findById($ussdSession->client_id);
                  }
               }
            }

            $txDTO->clientSurcharge = $client->surcharge; 
            $txDTO->testMSISDN = $client->testMSISDN;
            $txDTO->shortCode = $client->shortCode;
            $txDTO->urlPrefix = $client->urlPrefix;
            $txDTO->client_id = $client->id;
            
            if($client->mode != 'UP'){
               $txDTO->error = 'System in Maintenance Mode';
               $txDTO->response = \env('MODE_MESSAGE');
               $txDTO->exitPipeline = true;
               $txDTO->lastResponse = true;
            }

            if($client->status != 'ACTIVE'){
               $txDTO->response=\env('BLOCKED_MESSAGE')." ".\strtoupper($txDTO->urlPrefix);
               $txDTO->error = 'Client is blocked';
               $txDTO->lastResponse = true;
               $txDTO->exitPipeline = true;
            }

         } catch (\Throwable $e) {
            $txDTO->error = 'At identify client step. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
            $txDTO->handler = 'DummyMenu';
         }
      }
      return $txDTO;
      
   }

}

