<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            $billingClient = $billpaySettings['USE_BILLING_MOCK']=="YES"? 'MockBillingClient':$txDTO->billingClient;	
            if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);	
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 4) {
               $clientCaller = $billpaySettings['USE_BILLING_MOCK']=="YES"? 'mock':$txDTO->urlPrefix;
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);	
               App::bind(\App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient::class,'UpdateDetails_'.$clientCaller);
            }
            $stepHandler = App::make('UpdateDetails_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (\Throwable $e) {
            $txDTO->error = 'At handle customer field update menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
