<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayBusLevy implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     
         if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
            $billingClient = \env('USE_BILLING_MOCK')=="YES"? 'BillingMock':$txDTO->billingClient;
            App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
         }
         $stepHandler = App::make('PayBusLevy_Step_'.\count(\explode("*", $txDTO->customerJourney)));
         $txDTO = $stepHandler->run($txDTO);
      } catch (Exception $e) {
         $txDTO->error = 'At Bus levy steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}