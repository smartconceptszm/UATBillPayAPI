<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class MakeOtherPayment implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {   
         if($txDTO->error==''){  
            $step = \count(\explode("*", $txDTO->customerJourney)) - 1; 
            if ($step == 4) {
               $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
               $billingClient = $billpaySettings['USE_BILLING_MOCK_'.strtoupper($txDTO->urlPrefix)]=="YES"? 'MockBillingClient':$txDTO->billingClient;	
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);	
            }
            if($step >3){
               $customerJourney = explode("*", $txDTO->customerJourney);
               $txDTO->reference = $customerJourney[4];
            }
            $stepHandler = App::make('MakePayment_Step_'.$step);
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'At Make Payments menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }

}