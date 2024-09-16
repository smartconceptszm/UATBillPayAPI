<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;

class NextPage implements IUSSDMenu
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
		try {
         
				$responseNext = Cache::get($txDTO->sessionId."responseNext",'');
            Cache::forget($txDTO->sessionId."responseNext");
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $txDTO->subscriberInput = \end($arrCustomerJourney);
            \array_pop($arrCustomerJourney);

            if(\count($arrCustomerJourney) > 1){
               $txDTO->customerJourney = \implode("*", $arrCustomerJourney);
            }

            if(\count($arrCustomerJourney) == 1){
               $txDTO->customerJourney = $arrCustomerJourney[0];
            }

            if(\count($arrCustomerJourney) < 1){
               $selectedMenu = $this->clientMenuService->findOneBy([
                                             'client_id' => $txDTO->client_id,
                                             'parent_id' => 0
                                          ]);
               $txDTO->menu_id = $selectedMenu->id; 
               $txDTO->handler = $selectedMenu->handler; 
               $txDTO->menuPrompt = $selectedMenu->prompt; 
               $txDTO->customerJourney='';
            }

            $txDTO->response = $responseNext;

		} catch (\Throwable $e) {
			$txDTO->error = 'At handle next page response option. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
      
   }

    
}
