<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\Clients\AggregatedClientService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use App\Http\DTOs\BaseDTO;

class AggregatedClient
{
   
   public function __construct(
      private AggregatedClientService $aggregatedClientService,
      private ClientMenuService $clientMenuService,
      private ClientService $clientService)
   {}

   public function handle(BaseDTO $txDTO,$theSelectedMenu=null)
   {

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
      $txDTO->subscriberInput = $arrInputs[0]; 
      $txDTO->menuPrompt = $homeMenu->prompt;
      $txDTO->handler = $homeMenu->handler; 
   
      return $txDTO;
 
   }

}