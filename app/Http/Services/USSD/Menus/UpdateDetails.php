<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class UpdateDetails implements IUSSDMenu
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}


   public function handle(BaseDTO $txDTO):BaseDTO
   {
      
      
      try {
         if ($txDTO->error == '') {
	
            if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
               $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);	
            }
            if (\count(\explode("*", $txDTO->customerJourney)) == 4) {
               
               $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);	

               $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
               $clientCaller = $billpaySettings['USE_BILLING_MOCK_'.strtoupper($txDTO->urlPrefix)]=="YES"? 'mock':$txDTO->urlPrefix;
               App::bind(\App\Http\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient::class,'UpdateDetails_'.$clientCaller);
            }
            $stepHandler = App::make('UpdateDetails_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
         $txDTO->error = 'At handle customer field update menu. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }

      return $txDTO;
      
   }
    
}
