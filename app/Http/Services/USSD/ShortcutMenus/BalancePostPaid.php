<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\Sessions\ShortcutCustomerService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use App\Http\DTOs\BaseDTO;

class BalancePostPaid
{

   public function __construct(
      private ShortcutCustomerService $shortcutCustomerService,
      private ClientMenuService $clientMenuService,
      private ClientService $clientService)
   {}
   
   public function handle(BaseDTO $txDTO)
   {

      $customer = $this->shortcutCustomerService->findOneBy([
                                                         'mobileNumber' => $txDTO->mobileNumber,
                                                         'client_id' => $txDTO->client_id
                                                      ]);  
      if ($customer) { 
         $arrInputs = explode("*", $txDTO->subscriberInput);
         $selectedMenu = $this->clientMenuService->findOneBy([
                                                      'order' => $arrInputs[1],
                                                      'client_id' => $txDTO->client_id,
                                                      'parent_id' => $txDTO->menu_id,
                                                      'isActive' => "YES"
                                                   ]);
         $txDTO->billingClient = $selectedMenu->billingClient; 
         $txDTO->menuPrompt = $selectedMenu->prompt;
         $txDTO->handler = $selectedMenu->handler; 
         $txDTO->menu_id = $selectedMenu->id; 
         $txDTO->subscriberInput = $customer->customerAccount;
         $txDTO->customerJourney = $arrInputs[0] . "*" . $arrInputs[1];
      }
      return $txDTO;
       
   }

}