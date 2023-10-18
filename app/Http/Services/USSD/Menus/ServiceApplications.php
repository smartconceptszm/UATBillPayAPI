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
               $billingClient = \env('USE_BILLING_MOCK')=="YES"? 'BillingMock':$txDTO->urlPrefix;
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$billingClient);
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient::class,$txDTO->urlPrefix);
            }
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\Services\USSD\ServiceApplications\ServiceApplications_SubStep_1::class,
                  \App\Http\Services\USSD\ServiceApplications\ServiceApplications_SubStep_2::class,
                  \App\Http\Services\USSD\ServiceApplications\ServiceApplications_SubStep_3::class,
                  \App\Http\Services\USSD\ServiceApplications\ServiceApplications_SubStep_4::class,                    
                  \App\Http\Services\USSD\ServiceApplications\ServiceApplications_SubStep_5::class
               ]
            )
            ->thenReturn();
         } catch (Exception $e) {
            $txDTO->error='At handle service applications menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
