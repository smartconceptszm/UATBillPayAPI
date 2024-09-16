<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Web\Clients\AggregatedClientService;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\Web\Clients\ClientService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class AggregatedParentMenu implements IUSSDMenu
{

   public function __construct(
      private AggregatedClientService $aggregatedClientService,
      private ClientMenuService $clientMenuService,
      private ClientService $clientService)
   {}
   
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      if($txDTO->error==''){
         try {
            $client = $this->clientService->findById($txDTO->client_id);
            $aggregatedClient = $this->aggregatedClientService->findOneBy([
                                          'parent_id'=>$client->id,
                                          'menuNo'=>$txDTO->subscriberInput
                                       ]);
            if(!$aggregatedClient){
               throw new Exception("Invalid Menu Item number", 1);
            }
            $client = $this->clientService->findById($aggregatedClient->client_id);
            $txDTO->clientSurcharge = $client->surcharge; 
            $txDTO->testMSISDN = $client->testMSISDN;
            $txDTO->shortCode = $client->shortCode;
            $txDTO->urlPrefix = $client->urlPrefix;
            $txDTO->client_id = $client->id;

            $txDTO->subscriberInput = $txDTO->customerJourney;
            $txDTO->customerJourney = "";
				$homeMenu = $this->clientMenuService->findOneBy([
                                       'client_id'=>$txDTO->client_id,
                                       'parent_id'=>'0'
                                    ]);
            $txDTO->billingClient = $homeMenu->billingClient; 
            $txDTO->menuPrompt = $homeMenu->prompt;
            $txDTO->handler = $homeMenu->handler; 
            $txDTO->menu_id = $homeMenu->id; 

            $menus = $this->clientMenuService->findAll([
                                       'client_id'=>$txDTO->client_id,
                                       'parent_id'=>$homeMenu->id,
												   'isActive' => 'YES'
                                    ]);
            $prompt = $txDTO->menuPrompt."\n";
            foreach ($menus as $menu) {
               $prompt .= $menu->order.". ".$menu->prompt."\n";
            }
            $prompt .= "\n";
            $txDTO->response = $prompt;

         } catch (\Throwable $e) {
            if($e->getCode() == 1){
               $txDTO->error = $e->getMessage();
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->error='At handle aggregated parent menu. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
            }
         }
      }
      return $txDTO;
   }

}