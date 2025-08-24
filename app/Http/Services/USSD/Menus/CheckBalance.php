<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class CheckBalance implements IUSSDMenu
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}
   
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {
         if($txDTO->error==''){
            if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
               $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);
            }
            $stepHandler = App::make('CheckBalance_Step_'.count(explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
            $txDTO->error='At handle check balance menu. '.$e->getMessage();
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
   }

}