<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;

class NkanaOtherPayments implements IUSSDMenu
{
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     
         $stepHandler = App::make('NkanaOtherPayments_Step_'.\count(\explode("*", $txDTO->customerJourney)));
         $txDTO = $stepHandler->run($txDTO);
      } catch (\Throwable $e) {
         $txDTO->error = 'At Nkana Other Payments sub steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
    
}
