<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class CouncilPayment implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     
         $step = \count(\explode("*", $txDTO->customerJourney)) - 1;
         if($step >3){
            $customerJourney = explode("*", $txDTO->customerJourney);
            $txDTO->reference = $customerJourney[4];
         }
         
         $stepHandler = App::make('CouncilPayment_Step_'.$step);
         $txDTO = $stepHandler->run($txDTO);

      } catch (\Throwable $e) {
         $txDTO->error = 'At Council Payment menu. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}