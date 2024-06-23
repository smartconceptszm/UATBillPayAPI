<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class CheckBalance implements IUSSDMenu
{
   
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      if($txDTO->error==''){
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
               $enquiryHandler = \env('USE_BILLING_MOCK')=="YES"? 
                           'MockEnquiry':$txDTO->enquiryHandler;
               App::bind(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$enquiryHandler);
            }
            $stepHandler = App::make('CheckBalance_Step_'.count(explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (\Throwable $e) {
               $txDTO->error='At handle check balance menu. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
   }

}