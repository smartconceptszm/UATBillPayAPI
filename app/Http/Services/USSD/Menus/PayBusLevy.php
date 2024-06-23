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
            $enquiryHandler = \env('USE_BILLING_MOCK')=="YES"? 
                        'MockEnquiry':$txDTO->enquiryHandler;
            App::bind(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$enquiryHandler);
         }
         $stepHandler = App::make('PayBusLevy_Step_'.\count(\explode("*", $txDTO->customerJourney)));
         $txDTO = $stepHandler->run($txDTO);
      } catch (\Throwable $e) {
         $txDTO->error = 'At Bus levy steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}