<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Support\Facades\App;
use App\Http\DTOs\BaseDTO;
use Exception;

class VacuumTankerSwasco implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      if ($txDTO->error == '') {
         try {
            $stepHandler = App::make('VacuumTankerSwasco_Step_'.\count(\explode("*", $txDTO->customerJourney)));
            $txDTO = $stepHandler->run($txDTO);
         } catch (\Throwable $e) {
            $txDTO->error='At pay for vacuum tanker menu. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }
}
