<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Sessions\ShortcutCustomerService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayPostPaidBill
{

   public function __construct(
      private ShortcutCustomerService $shortcutCustomerService,
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ClientMenuService $clientMenuService)
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
         $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if(!$momoPaymentStatus['enabled']){
            throw new Exception($momoPaymentStatus['responseText'], 2);
         }
         $txDTO->subscriberInput = $arrInputs[2];
         $txDTO->customerAccount = $customer->customerAccount;
         $txDTO->customerJourney = $arrInputs[0] . "*" . $arrInputs[1] . "*" .
                                       $customer->customerAccount. "*" . $customer->customerAccount;
      }
      return $txDTO;
       
   }

}