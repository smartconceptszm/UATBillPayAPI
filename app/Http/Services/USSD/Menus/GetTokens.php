<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class GetTokens implements IUSSDMenu
{
   
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {
         if($txDTO->error==''){
            $stepHandler = App::make('GetTokens_Step_'.count(explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         }
      } catch (\Throwable $e) {
            $txDTO->error='At handle get tokens menu. '.$e->getMessage();
            $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
   }

}