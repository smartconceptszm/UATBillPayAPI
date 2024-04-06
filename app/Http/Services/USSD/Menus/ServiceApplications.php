<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;


class ServiceApplications implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
               $enquiryHandler = \env('USE_BILLING_MOCK')=="YES"? 
                           'MockEnquiry':$txDTO->enquiryHandler;
               App::bind(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$enquiryHandler);
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient::class,$txDTO->urlPrefix);
            }
            $stepHandler = App::make('ServiceApplications_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);

         } catch (\Throwable $e) {
            $txDTO->error='At handle service applications menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
