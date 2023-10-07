<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientService; 
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_IdentifyClient extends EfectivoPipelineContract
{
    
   public function __construct(
      private ClientService $clientService)
   {}
   
   protected function stepProcess(BaseDTO $txDTO)
   {

      if($txDTO->error == ''){ 
         try {
            $client = $this->clientService->findOneBy(['urlPrefix'=>$txDTO->urlPrefix]);
            $txDTO->client_id = $client->id;
            $txDTO->clientCode = $client->code;
            $txDTO->clientSurcharge = $client->surcharge;
            if($client->mode != 'UP'){
               $txDTO->error = 'System in Maintenance Mode';
               $txDTO->errorType = "MaintenanceMode";
               return $txDTO;
            }
            if($client->status != 'ACTIVE'){
               $txDTO->error = 'Client is blocked';
               $txDTO->errorType = "ClientBlocked";
               return $txDTO;
            }
         } catch (Exception $e) {
            $txDTO->error = 'At identify client step. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }

}

