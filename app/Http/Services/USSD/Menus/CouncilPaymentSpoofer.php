<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class CouncilPaymentSpoofer implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     
         $step = \count(\explode("*", $txDTO->customerJourney));
         if($step >3){
            $coustomerJourney = explode("*", $txDTO->customerJourney);
            $txDTO->reference = $coustomerJourney[3];
         }
         $stepHandler = App::make('CouncilPaymentSpoofer_Step_'.$step);
         $txDTO = $stepHandler->run($txDTO);
      } catch (\Throwable $e) {
         $txDTO->error = 'At Council Proxy Payment menu. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}