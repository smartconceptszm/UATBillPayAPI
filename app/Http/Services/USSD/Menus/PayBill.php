<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class PayBill implements IUSSDMenu
{
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      try {     
         $txDTO->stepProcessed=false;
         $txDTO = \app(Pipeline::class)
               ->send($txDTO)
               ->through(
                  [
                     \App\Http\Services\USSD\PayBill\Payments_SubStep_1::class,
                     \App\Http\Services\USSD\PayBill\Payments_SubStep_2::class,
                     \App\Http\Services\USSD\PayBill\Payments_SubStep_3::class,
                     \App\Http\Services\USSD\PayBill\Payments_SubStep_4::class,
                     \App\Http\Services\USSD\PayBill\Payments_SubStep_5::class
                  ]
               )
               ->thenReturn();
         $txDTO->stepProcessed=false;
      } catch (Exception $e) {
         $txDTO->error = 'At pay bill sub steps. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;
      
   }
    
}
