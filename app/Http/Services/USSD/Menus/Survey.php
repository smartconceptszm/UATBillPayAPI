<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class Survey implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      if ($txDTO->error == '') {
         try {
            if (\count(\explode("*", $txDTO->customerJourney)) == 2 || \count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\External\BillingClients\IBillingClient::class,$txDTO->urlPrefix);
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\USSD\Survey\ClientCallers\ISurveyClient::class,$txDTO->urlPrefix);
            }
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                  \App\Http\Services\USSD\Survey\Survey_SubStep_1::class,
                  \App\Http\Services\USSD\Survey\Survey_SubStep_2::class,
                  \App\Http\Services\USSD\Survey\Survey_SubStep_3::class,
                  \App\Http\Services\USSD\Survey\Survey_SubStep_5::class
               ]
            )
            ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (Exception $e) {
            $txDTO->error = 'At handle survey menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;
      
   }
    
}
