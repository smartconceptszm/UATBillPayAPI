<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class Survey implements IUSSDMenu
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      try {
         if ($txDTO->error == '') {
            if (\count(\explode("*", $txDTO->customerJourney)) == 2 || \count(\explode("*", $txDTO->customerJourney)) == 5) {
               $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);	
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\USSD\Survey\ClientCallers\ISurveyClient::class,'Survey_'.$txDTO->urlPrefix);
            }
            $stepHandler = App::make('Survey_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'At handle survey menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }
    
}
