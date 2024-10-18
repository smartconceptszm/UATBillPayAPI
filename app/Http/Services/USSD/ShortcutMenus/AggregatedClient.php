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

   public function handle(BaseDTO $txDTO)
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
      if(\count($arrInputs)==2){
         $txDTO->billingClient = $homeMenu->billingClient; 
         $txDTO->subscriberInput = $arrInputs[0]; 
         $txDTO->menuPrompt = $homeMenu->prompt;
         $txDTO->handler = $homeMenu->handler; 
      }

      if(\count($arrInputs)>2){
         $selectedMenu = $this->clientMenuService->findOneBy([
                                       'order' => $arrInputs[2],
                                       'client_id' => $txDTO->client_id,
                                       'parent_id' => $txDTO->menu_id,
                                       'isActive' => "YES"
                                    ]); 
         $txDTO->billingClient = $selectedMenu->billingClient; 
         $txDTO->menuPrompt = $selectedMenu->prompt;
         $txDTO->handler = $selectedMenu->handler; 
         $txDTO->customerJourney = $arrInputs[0]*$arrInputs[1]; 
         $txDTO->subscriberInput = $arrInputs[2]; 
         $txDTO->menu_id = $selectedMenu->id; 
      }

      return $txDTO;


       
   }

}