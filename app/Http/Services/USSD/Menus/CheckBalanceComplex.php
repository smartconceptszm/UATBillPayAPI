<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Utility\SCLExternalServiceBinder;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class CheckBalanceComplex implements IUSSDMenu
{

   public function __construct(
      private SCLExternalServiceBinder $sclExternalServiceBinder
   ) {}
   
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {
         if($txDTO->error==''){
            $stepCount = \count(\explode("*", $txDTO->customerJourney)) -1;
            if ($stepCount == 2) {
               $this->sclExternalServiceBinder->bindBillingClient($txDTO->urlPrefix,$txDTO->menu_id);
            }
            $stepHandler = App::make('CheckBalance_Step_'.$stepCount);
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
            $txDTO->error='At handle check balance menu. '.$e->getMessage();
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }

      return $txDTO;
   }

}