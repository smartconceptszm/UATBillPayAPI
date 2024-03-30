<?php

namespace App\Http\Services\Web\WebPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
USE App\Http\Services\Clients\ClientService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_GetClientDetails extends EfectivoPipelineContract
{

   public function __construct(
      private ClientService $clientService)
   {}

   protected function stepProcess(BaseDTO $webDTO)
   {

      try {
         $client = $this->clientService->findOneBy(['urlPrefix'=>$webDTO->urlPrefix]);
         $client = \is_null($client)?null:(object)$client->toArray();  
         $webDTO->client_id = $client->id;
         $webDTO->shortCode = $client->shortCode;
         $webDTO->testMSISDN = $client->testMSISDN;
         $webDTO->clientSurcharge = $client->surcharge;
         if($client->mode != 'UP'){
            throw new Exception(\env('MODE_MESSAGE'),1);
         }
         if($client->status != 'ACTIVE'){
            throw new Exception(\env('BLOCKED_MESSAGE')." ".strtoupper($webDTO->urlPrefix),1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $webDTO->error = $e->getMessage();
         }else{
            $webDTO->error = 'At get web client details. '.$e->getMessage();
         }
      }
      return $webDTO;

   }
   
}