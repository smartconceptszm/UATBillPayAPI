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
            if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
               $billingClient = \env('USE_BILLING_MOCK')=="YES"? 'BillingMock':$txDTO->billingClient;
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 4) {
               App::bind(\App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient::class,$txDTO->urlPrefix);
            }
            $stepHandler = App::make('UpdateDetails_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (Exception $e) {
            $txDTO->error = 'At handle customer field update menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
