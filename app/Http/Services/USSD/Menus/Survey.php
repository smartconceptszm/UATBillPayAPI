<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class Survey implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 2 || \count(\explode("*", $txDTO->customerJourney)) == 5) {
               $enquiryHandler = \env('USE_BILLING_MOCK')=="YES"? 
                           'MockEnquiry':$txDTO->enquiryHandler;
               App::bind(\App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$enquiryHandler);
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\USSD\Survey\ClientCallers\ISurveyClient::class,'Survey_'.$txDTO->urlPrefix);
            }
            $stepHandler = App::make('Survey_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (\Throwable $e) {
            $txDTO->error = 'At handle survey menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
