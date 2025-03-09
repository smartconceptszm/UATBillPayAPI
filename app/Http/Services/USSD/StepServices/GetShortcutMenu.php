<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Clients\AggregatedClientService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;

class GetShortcutMenu
{

	public function __construct(
      private AggregatedClientService $aggregatedClientService,
		private ClientMenuService $clientMenuService)
	{}

   public function handle(BaseDTO $txDTO):object|null
   {

      try {

         $arrInputs = explode("*", $txDTO->subscriberInput);
         $arrInputs = array_slice($arrInputs,0,2);
         if($txDTO->ussdAggregator == 'YES'){
            $theClient = $this->aggregatedClientService->findOneBy(['client_id' =>$txDTO->client_id]);
            array_splice($arrInputs,1,0,(string)$theClient->menuNo);
         }
         $strShortcut = implode('*',$arrInputs)."*(AMOUNT)#";
         return $this->clientMenuService->findOneBy(['client_id' =>$txDTO->client_id, 'shortcut'=>$strShortcut]);
         
      } catch (\Throwable $e) {
         throw $e;
      }
      
   }

    
    
}