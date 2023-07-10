<?php

namespace App\Http\BillPay\Services\USSD\Menus;

use App\Http\BillPay\Services\USSD\Menus\IUSSDMenu;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class CheckBalance implements IUSSDMenu
{
    
   public function handle(BaseDTO $txDTO):BaseDTO
   {

      if($txDTO->error==''){
         try {
            $txDTO->stepProcessed=false;
            $txDTO = app(Pipeline::class)
                  ->send($txDTO)
                  ->through(
                     [ 
                        \App\Http\BillPay\Services\USSD\CheckBalance\CheckBalance_SubStep_1::class,
                        \App\Http\BillPay\Services\USSD\CheckBalance\CheckBalance_SubStep_2::class,
                        \App\Http\BillPay\Services\USSD\CheckBalance\CheckBalance_SubStep_3::class,
                        \App\Http\BillPay\Services\USSD\CheckBalance\CheckBalance_SubStep_4::class,
                        \App\Http\BillPay\Services\USSD\CheckBalance\CheckBalance_SubStep_5::class
                     ]
                  )
                  ->thenReturn();
            $txDTO->stepProcessed=false;
         } catch (\Throwable $e) {
               $txDTO->error='At handle check balance menu. '.$e->getMessage();
               $txDTO->errorType = 'SystemError';
         }
      }
      return $txDTO;

   }

}