<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\AggregatedClientService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;

use App\Http\DTOs\BaseDTO;
use Exception;

class GetAggregatedClient extends EfectivoPipelineContract
{

   public function __construct(
      private AggregatedClientService $aggregatedClientService,
      private ClientMenuService $clientMenuService,
      private ClientService $clientService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if(($txDTO->isNewRequest == '1') 
                  && $txDTO->ussdAggregator == 'YES'
                     && (\count(\explode("*", $txDTO->subscriberInput))>1)){
            

            $arrInputs = explode("*", $txDTO->subscriberInput);
            $aggregatedClient = $this->aggregatedClientService->findOneBy([
                                             'parent_id' => $txDTO->client_id,
                                             'menuNo' => $arrInputs[1]
                                          ]);
            if(!$aggregatedClient){
               return $txDTO;
            }

            $client = $this->clientService->findById($aggregatedClient->client_id);
            $txDTO->clientSurcharge = $client->surcharge; 
            $txDTO->testMSISDN = $client->testMSISDN;
            $txDTO->shortCode = $client->shortCode;
            $txDTO->urlPrefix = $client->urlPrefix;
            $txDTO->client_id = $client->id;

            $homeMenu = $this->clientMenuService->findOneBy([
                                                      'client_id' => $txDTO->client_id,
                                                      'parent_id' =>'0'
                                                   ]);
            $txDTO->menu_id = $homeMenu->id;
            $txDTO->billingClient = $homeMenu->billingClient; 
            $txDTO->menuPrompt = $homeMenu->prompt;
            $txDTO->handler = $homeMenu->handler; 

            array_splice($arrInputs,1,1);
            $txDTO->subscriberInput =  implode('*',$arrInputs); 

         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}