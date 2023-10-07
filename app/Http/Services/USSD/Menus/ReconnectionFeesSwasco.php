<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class ReconnectionFeesSwasco implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      if ($txDTO->error == '') {
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 4) {
               //Bind selected Billing Client to the Interface
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$txDTO->urlPrefix);
            }
            $stepHandler = App::make('ReconnectionFeesSwasco_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (Exception $e) {
            $txDTO->error='At pay reconnection fees menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
}
