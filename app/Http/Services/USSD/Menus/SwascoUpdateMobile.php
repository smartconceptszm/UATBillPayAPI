<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class SwascoUpdateMobile implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 2 || \count(\explode("*", $txDTO->customerJourney)) == 3) {
               $billingClient = \env('USE_BILLING_MOCK')=="YES"? 'BillingMock':$txDTO->billingClient;
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
               App::bind(\App\Http\Services\ExternalAdaptors\BillingEnquiryHandlers\IEnquiryHandler::class,$txDTO->urlPrefix.$txDTO->accountType."Enquiry");
            }
            $stepHandler = App::make('SwascoUpdateMobile_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (\Throwable $e) {
            $txDTO->error = 'At handle customer field update menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
