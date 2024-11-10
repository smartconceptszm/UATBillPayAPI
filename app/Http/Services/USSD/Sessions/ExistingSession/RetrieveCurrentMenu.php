<?php

namespace App\Http\Services\USSD\Sessions\ExistingSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class RetrieveCurrentMenu extends EfectivoPipelineContract
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         $currentMenu = $this->clientMenuService->findById($txDTO->menu_id);

         if($txDTO->subscriberInput === '00' && cache($txDTO->sessionId."responseNext",'') ){
            $currentMenu->handler = 'NextPage';
         }

         if($currentMenu->handler == 'ParentMenu'){
            $currentMenu = $this->clientMenuService->findOneBy([
                                 'order' => $txDTO->subscriberInput,
                                 'client_id' => $txDTO->client_id,
                                 'parent_id' => $txDTO->menu_id,
                                 'isActive' => "YES"
                              ]);
            if(!$currentMenu){
               throw new Exception("Invalid Menu Item number", 1);
            }
         }

         $txDTO->billingClient = $currentMenu->billingClient; 
         $txDTO->menuPrompt = $currentMenu->prompt;
         $txDTO->handler = $currentMenu->handler; 
         $txDTO->menu_id = $currentMenu->id;

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}