<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetSpoofedMenu 
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function handle(BaseDTO $txDTO)
   {

      try {    
         $customerJourney = \explode("*", $txDTO->customerJourney);
         $order = \count($customerJourney) >2?  $customerJourney[2]:$txDTO->subscriberInput;
         $rootMenu = $this->clientMenuService->findOneBy([
                                                   'client_id' => $txDTO->client_id,
                                                   'parent_id' =>'0'
                                                ]);
         $clientMenu = $this->clientMenuService->findOneBy([
                                                   'order' => $order,
                                                   'client_id' => $txDTO->client_id,
                                                   'parent_id' => $rootMenu->id,
                                                   'isActive' => "YES"
                                                ]);
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $clientMenu;
      
   }
   
}