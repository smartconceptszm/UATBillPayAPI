<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

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
                     \App\Http\BillPay\Services\USSD\BuyUnits\BuyUnits_SubStep_1::class,
                     \App\Http\BillPay\Services\USSD\BuyUnits\BuyUnits_SubStep_2::class,
                     \App\Http\BillPay\Services\USSD\BuyUnits\BuyUnits_SubStep_3::class,
                     \App\Http\BillPay\Services\USSD\BuyUnits\BuyUnits_SubStep_4::class,
                     \App\Http\BillPay\Services\USSD\BuyUnits\BuyUnits_SubStep_5::class
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