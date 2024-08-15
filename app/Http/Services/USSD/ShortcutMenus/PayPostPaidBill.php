<?php

namespace App\Http\Services\USSD\ShortcutMenus;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Web\Sessions\ShortcutCustomerService;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayPostPaidBill
{

   public function __construct(
      private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ShortcutCustomerService $shortcutCustomerService)
   {}
   
   public function handle(BaseDTO $txDTO, object $selectedMenu)
   {

      $arrInputs = explode("*", $txDTO->subscriberInput);
      $customer = $this->shortcutCustomerService->findOneBy([
                                                         'mobileNumber' => $txDTO->mobileNumber,
                                                         'client_id' => $txDTO->client_id
                                                      ]);  
                                                      
      if (!\is_null($customer)) { 
         $txDTO->billingClient = $selectedMenu->billingClient; 
         $txDTO->menuPrompt = $selectedMenu->prompt;
         $txDTO->handler = $selectedMenu->handler; 
         $txDTO->menu_id = $selectedMenu->id; 
         $momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
         if(!$momoPaymentStatus['enabled']){
            throw new Exception($momoPaymentStatus['responseText'], 2);
         }
         if (\count($arrInputs) == 2) {
            $txDTO->subscriberInput = $customer->accountNumber;
            $txDTO->customerJourney = $arrInputs[0] . "*" . $arrInputs[1];
            return $txDTO;
         }
         if (\count($arrInputs) == 3) {
            $txDTO->subscriberInput = $arrInputs[2];
            $txDTO->accountNumber = $customer->accountNumber;
            $txDTO->customerJourney = $arrInputs[0] . "*" . $arrInputs[1] . "*" . $customer->accountNumber;
            return $txDTO;
         }
      }
      return $txDTO;
       
   }

}