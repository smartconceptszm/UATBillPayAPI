<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class BuyUnits implements IUSSDMenu
{

   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     
         $txDTO->stepProcessed=false;
         $txDTO = \app(Pipeline::class)
            ->send($txDTO)
            ->through(
               [
                     \App\Http\Services\USSD\BuyUnits\BuyUnits_SubStep_1::class,
                     \App\Http\Services\USSD\BuyUnits\BuyUnits_SubStep_2::class,
                     \App\Http\Services\USSD\BuyUnits\BuyUnits_SubStep_3::class,
                     \App\Http\Services\USSD\BuyUnits\BuyUnits_SubStep_4::class,
                     \App\Http\Services\USSD\BuyUnits\BuyUnits_SubStep_5::class
               ]
            )
            ->thenReturn();
         $txDTO->stepProcessed=false;
      } catch (\Throwable $e) {
         $txDTO->error = 'At buy units sub steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }

}