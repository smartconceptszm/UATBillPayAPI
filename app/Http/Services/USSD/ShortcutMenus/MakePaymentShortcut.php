<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Payments\ShortcutCustomerService;
use App\Http\DTOs\BaseDTO;
use Exception;

class MakePaymentShortcut
{

   public function __construct(
      private ShortcutCustomerService $shortcutCustomerService,
      private CheckPaymentsEnabled $checkPaymentsEnabled)
   {}
   
   public function handle(BaseDTO $txDTO, $selectedMenu)
   {

      $arrInputs = explode("*", $txDTO->subscriberInput);
      if(count($arrInputs) != 3){
         $txDTO->subscriberInput = $arrInputs[0];
         return $txDTO;
      }

      $customer = $this->shortcutCustomerService->findOneBy([
                                                         'mobileNumber' => $txDTO->mobileNumber,
                                                         'client_id' => $txDTO->client_id
                                                      ]);  
                                                      
      if (!$customer) { 
         $txDTO->subscriberInput = $arrInputs[0];
         return $txDTO;
      }

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
                                    $customer->customerAccount. "*" . $customer->mobileNumber;
         
      return $txDTO;
       
   }

}