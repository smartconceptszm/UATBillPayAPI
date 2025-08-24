<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Utility\SCLExternalServiceBinder;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;


class ServiceApplications implements IUSSDMenu
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}

   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      try {
         if ($txDTO->error == '') {
            if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
               $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);	
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
               App::bind(\App\Http\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient::class,$txDTO->urlPrefix);
            }
            $stepHandler = App::make('ServiceApplications_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
         $txDTO->error='At handle service applications menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }
    
}
