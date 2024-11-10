<?php

namespace App\Http\Services\USSD\Sessions\NewSession;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Sessions\SessionService;
use App\Http\DTOs\BaseDTO;
use Exception;

class CreateSession extends EfectivoPipelineContract
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private SessionService $sessionService)
   {}


   protected function stepProcess(BaseDTO $txDTO)
   {

      try {

         if($txDTO->isNewRequest == '1'){
            $homeMenu = $this->clientMenuService->findOneBy([
                                                            'client_id' => $txDTO->client_id,
                                                            'parent_id' =>'0'
                                                         ]);
            $txDTO->billingClient = $homeMenu->billingClient; 
            $txDTO->menuPrompt = $homeMenu->prompt;
            $txDTO->handler = $homeMenu->handler; 
            $txDTO->menu_id = $homeMenu->id; 
            $ussdSession = $this->sessionService->create($txDTO->toSessionData());
            $txDTO->created_at = $ussdSession->created_at;
            $txDTO->id = $ussdSession->id;
         }

      } catch (\Throwable $e) {
         throw $e;
      }

      return $txDTO;
      
   }

}