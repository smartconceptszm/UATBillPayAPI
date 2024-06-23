<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayBill implements IUSSDMenu
{
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     

         if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
            $enquiryHandler = \env('USE_BILLING_MOCK')=="YES"? 
                        'MockEnquiry':$txDTO->enquiryHandler;
            App::bind(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$enquiryHandler);
         }
         $stepHandler = App::make('PayBill_Step_'.\count(\explode("*", $txDTO->customerJourney)));
         $txDTO = $stepHandler->run($txDTO);
      } catch (\Throwable $e) {
         $txDTO->error = 'At pay bill sub steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
    
}
